<?php defined('SYSPATH') OR die('No direct script access.');

interface API_Response_Item {

    /**
     * @return array|Traversable
     */
    public function get_api_response_data();

    /**
     * @return DateTime|NULL
     */
    public function get_last_modified();

}