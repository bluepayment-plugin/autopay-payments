<?php

namespace Ilabs\BM_Woocommerce\Utilities\Test_Connection;

use Exception;
use Ilabs\BM_Woocommerce\Controller\Controller_Interface;
use Isolated\BlueMedia\Ilabs\Ilabs_Plugin\File_System\File;
use Isolated\BlueMedia\Ilabs\Ilabs_Plugin\File_System\File_Downloader;

class Async_Request implements Controller_Interface {

	const AJAX_ACTION_NAME = 'autopay_connection_test';

	const REQUEST_ACTION_NAME_NEW = 'new';

	const REQUEST_ACTION_NAME_CONTINUE = 'continue';


	/**
	 * @throws Exception
	 */
	public function execute_request() {
		try {
			error_reporting( E_ALL & ~E_NOTICE & ~E_WARNING & ~E_DEPRECATED );
			ini_set( 'display_errors', 1 );

			add_filter( 'autopay_log_id', function ( $id ) {
				return 'bm_woocommerce_audit';
			} );

			blue_media()->get_woocommerce_logger()->log_debug(
				sprintf( '[Async_Request] [execute_request] [POST: %s]',
					print_r( $_POST, true )
				) );

			if ( ! isset( $_POST['nonce'] ) ) {
				throw new Exception( __( 'Nonce field not exists',
					'bm-woocommerce' ) );
			}


			$nonce = sanitize_text_field( $_POST['nonce'] );
			if ( ! wp_verify_nonce( $nonce, self::get_nonce_action() ) ) {
				throw new Exception( __( 'Verification nonce failed',
					'bm-woocommerce' ) );
			}

			if ( ! isset( $_POST['autopay_action'] ) ) {
				throw new Exception( __( 'Action field not exists',
					'bm-woocommerce' ) );
			}


			$action = sanitize_text_field( $_POST['autopay_action'] );

			switch ( $action ) {
				case 'new':

					$auditor = Auditor::create_new();
					$test_id = $auditor->get_id();

					$response = new Response_Continue( $test_id,
						Strings::get_strings()['serverTest'],
						'' );
					break;

				case 'continue':

					if ( ! isset( $_POST['test_id'] ) ) {
						throw new Exception( __( 'test_id field not exists',
							'bm-woocommerce' ) );
					}
					$test_id    = sanitize_text_field( $_POST['test_id'] );
					$auditor    = Auditor::load( $test_id );
					$log        = $auditor->run();
					$stage_name = $auditor->get_stageName();


					if ( $auditor->is_finished() ) {
						$response = new Response_Finished( $test_id,
							$stage_name );

						if ( $auditor->is_failed() ) {
							$message = __( 'Your store is not ready to accept payments. Please check the logs for more details.',
								'bm-woocommerce' );
							$summary = new Summary( $message );
							$response->set_summary_error( $summary );
						} elseif ( $auditor->is_warning() ) {
							$message = __( 'Minor inconsistencies in your shop were detected, though they may not block transactions entirely. Review your integration steps to optimize performance. If you encounter issues, you can share the log file by downloading or copying it.',
								'bm-woocommerce' );
							$summary = new Summary( $message );
							$response->set_summary_success( $summary );
						} else {
							$message = __( 'All gateway prerequisites were met, and transactions should proceed smoothly. Keep this success summary so you can reference it if updates are needed.',
								'bm-woocommerce' );
							$summary = new Summary( $message );
							$response->set_summary_success( $summary );
						}

						if ( $auditor->is_zip_not_found() ) {
							$logs_url = '';
						} else {
							$logs_url = $this->get_logs_url();
						}

						$response->set_wc_log_url( $logs_url );


					} else {
						if ( $auditor->is_zip_not_found() ) {
							$logs_url = '';
						} else {
							$logs_url = $this->get_logs_url();
						}

						$response = new Response_Continue( $test_id,
							$stage_name,
							$logs_url );
					}
					$response->set_log( $log );

					break;

				default:

					throw new Exception( __( 'Invalid action',
						'bm-woocommerce' ) );
			}

			blue_media()
				->get_woocommerce_logger()
				->log_debug(
					sprintf( '[Connection_Testing_Controller] [execute_request] [Auditor: %s]',
						print_r( $auditor, true )
					) );


			$response_arr = $response->to_array();
			blue_media()
				->get_woocommerce_logger()
				->log_debug(
					sprintf( '[Connection_Testing_Controller] [execute_request] [Response: %s]',
						print_r( $response_arr, true )
					) );

			wp_send_json( $response_arr );
			die;

		} catch ( Exception $exception ) {
			blue_media()->get_woocommerce_logger()->log_debug(
				sprintf( '[Connection_Testing_Controller] [execute_request] [Message: %s] [POST: %s] ',
					$exception->getMessage(),
					print_r( $_POST, true )
				) );

			echo wp_json_encode( [
				'status'  => 'error',
				'message' => $exception->getMessage(),
			] );
			exit;
		}

	}

	public function get_logs_url() {
		$logs = $this->get_autopay_audit_logs();
		blue_media()
			->get_woocommerce_logger( 'testing' )
			->log_debug(
				sprintf( '[Connection_Testing_Controller] [logs %s] ',
					print_r( $logs, true )
				) );

		$files = [];
		foreach ( $logs as $k => $log ) {
			$file    = new File( 'log_' . $k . '.txt', $log );
			$files[] = $file;
		}

		$downloader = new File_Downloader();

		return $downloader->get_download_url( $files, 3000 );

	}

	private function output_response(
		Response_Interface $response
	) {
		echo wp_json_encode( $response->to_array() );
		exit;
	}

	public function handle() {
		add_action( 'wp_ajax_' . self::AJAX_ACTION_NAME,
			function () {
				$this->execute_request();
			} );

		add_action( 'wp_ajax_nopriv_' . self::AJAX_ACTION_NAME,
			function () {
				$this->execute_request();
			} );
	}

	public static function generate_nonce(): string {
		return wp_create_nonce( self::get_nonce_action() );
	}

	public static function get_nonce_action(): string {
		return 'autopay_audit_';
	}


	public function get_autopay_audit_logs() {

		$upload_dir = wp_upload_dir();


		$log_dir = trailingslashit( $upload_dir['basedir'] ) . 'wc-logs';


		if ( ! is_dir( $log_dir ) ) {
			return [];
		}

		$pattern = trailingslashit( $log_dir ) . '*bm_woocommerce*.log';


		$files = glob( $pattern );


		if ( false === $files ) {
			$files = [];
		}

		return $files;
	}
}
