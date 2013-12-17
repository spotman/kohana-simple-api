<?php defined('SYSPATH') OR die('No direct script access.');

interface API_Model_Result {

    /**
     * @return array|Traversable
     */
    public function get_api_result_data();

}