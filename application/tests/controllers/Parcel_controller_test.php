<?php

class Parcel_controller_test extends TestCase
{
	/**
	 * Set $strictRequestErrorCheck false, because the following error occurs if true
	 *
	 * 1) Example_test::test_users_get_format_csv
	 * RuntimeException: Array to string conversion on line 373 in file .../application/libraries/Format.php
	 *
	 * @var bool
	 */
	protected $strictRequestErrorCheck = false;

	public function test_create_parcel()
	{
		try {
			$output = $this->request('POST', 'parcels');
		} catch (CIPHPUnitTestExitException $e) {
			$output = ob_get_clean();
		}
		$this->assertEquals(
			'{ "status": 201,"message": "Parcel data has been saved."}',
			$output
		);
		$this->assertResponseCode(201);
	}

	public function test_get_detail_parcel()
	{
		$output = $this->request('GET', 'parcels/7');
		$this->assertEquals(
			'[{"parcel_id": "7",   "parcel_customer_id": "1",   "parcel_name": "new parcel 2",   "parcel_weight": "22",   "parcel_volume": "222",   "parcel_declared_value": "2222",   "parcel_chosen_model": "BY_VOLUME",   "parcel_quote": "222000"    }]',
			$output
		);
		$this->assertResponseCode(200);
	}

	public function test_update_parcel()
	{
		$output = $this->request(
			'PUT',
			'parcels/1'
		);
		$this->assertEquals(
			'{"status": 201,"message": "Parcel data has been updated."}',
			$output
		);
		$this->assertResponseCode(201);
	}

	public function test_delete_parcel()
	{
		$output = $this->request(
			'DELETE',
			'parcels/1'
		);
		$this->assertEquals(
			'{"status": 200,"message": "Parcel data has been deleted."}',
			$output
		);
		$this->assertResponseCode(200);
	}

}
