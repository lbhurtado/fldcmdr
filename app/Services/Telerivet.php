<?php

namespace App\Services;

class Telerivet
{
    private $api;

    private $project;

    private $service_id;

    public function __construct($api_key, $project_id, $service_id = null)
    {
        $this->api = new \Telerivet_API($api_key);
        $this->project = $this->api->initProjectById($project_id);
        $this->service_id = $service_id;
    }

    public function getProject()
    {
    	return $this->project;
    }

    public function getService()
    {
        return $this->getProject()->initServiceById($this->service_id);
    }
}
