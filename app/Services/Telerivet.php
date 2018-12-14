<?php

namespace App\Services;

class Telerivet
{
    private $api;

    private $project;

    private $service_id;

    public function __construct()
    {
        $config = config('broadcasting.connections.telerivet');

        $this->setApi($config['api_key']);
        $this->setProject($config['project_id']);
    }

    protected function setApi($api_key)
    {
        $this->api = new \Telerivet_API($api_key);

        return $this;
    }

    protected function getApi()
    {
        return $this->api;
    }

    protected function setProject($project_id)
    {
        $this->project = $this->getApi()->initProjectById($project_id);

        return $this;
    }

    public function setCampaign($campaign)
    {
        $this->service_id = config("chatbot.campaigns.{$campaign}");

        return $this;
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
