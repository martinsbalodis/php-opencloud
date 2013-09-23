<?php
/**
 * PHP OpenCloud library.
 * 
 * @copyright Copyright 2013 Rackspace US, Inc. See COPYING for licensing information.
 * @license   https://www.apache.org/licenses/LICENSE-2.0 Apache 2.0
 * @version   1.6.0
 * @author    Jamie Hannaford <jamie.hannaford@rackspace.com>
 */

namespace OpenCloud\Autoscale\Resource;

use OpenCloud\Common\Exceptions;

/**
 * An autoscaling group is monitored by Rackspace Cloud Monitoring. When 
 * Monitoring triggers an alarm for high utilization within the autoscaling 
 * group, a webhook is triggered. The webhook stimulates the autoscale service 
 * which consults a policy in accordance with the webhook. The policy determines 
 * how many additional cloud servers should be added or removed in accordance 
 * with the alarm.
 * 
 * There are three components to Autoscale:
 * 
 * - The Scaling Group Configuration ($this->groupConfiguration)
 * - The Scaling Group's Launch Configuration ($this->launchConfiguration)
 * - The Scaling Group's Policies ($this->scalingPolicies)
 * 
 * @link https://github.com/rackerlabs/otter/blob/master/doc/getting_started.rst
 * @link http://docs.autoscale.apiary.io/
 */
class Group extends AbstractResource
{
    
    private $id;
    private $links;
    private $groupConfiguration;
    private $launchConfiguration;
    private $scalingPolicies;
    private $name;
    protected $metadata;
    
    private $active;
    private $activeCapacity;
    private $pendingCapacity;
    private $desiredCapacity;
    private $paused;
    
    protected static $json_name = 'group';
    protected static $url_resource = 'groups';
    protected static $json_collection_name = 'groups';
    
    /**
     * {@inheritDoc}
     */
    public $createKeys = array(
        'groupConfiguration',
        'launchConfiguration',
        'scalingPolicies'
    );
    
    /**
     * {@inheritDoc}
     */
    public $associatedResources = array(
        'groupConfiguration'  => 'GroupConfiguration',
        'launchConfiguration' => 'LaunchConfiguration',
        
    );
    
    /**
     * {@inheritDoc}
     */
    public $associatedCollections = array(
        'scalingPolicies' => 'ScalingPolicy'
    );
        
    /**
     * {@inheritDoc}
     */
    public function update($params = array())
    {
        return $this->noUpdate();
    }
    
    /**
     * Get the current state of the scaling group, including the current set of 
     * active entities, the number of pending entities, and the desired number 
     * of entities.
     * 
     * @return object|boolean
     * @throws Exceptions\HttpError
     * @throws Exceptions\ServerActionError
     */
    public function getState()
    {
        $object = $this->customAction($this->url('state', true));
        
        return (!empty($object->group)) ? $object->group : false;
    }
    
    /**
     * Get the group configuration for this autoscale group.
     * 
     * @return GroupConfiguration
     */
    public function getGroupConfig()
    {
        if (($config = $this->getProperty('groupConfiguration')) instanceof GroupConfiguration) {
            return $config;
        }
        
        $config = $this->getService()->resource('GroupConfiguration');
        $config->setParent($this);
        if ($this->getId()) {
            $config->refresh(null, $config->url());
        }
        return $config;
    }
    
    /**
     * Get the launch configuration for this autoscale group.
     * 
     * @return LaunchConfiguration
     */
    public function getLaunchConfig()
    {
        if (($config = $this->getProperty('launchConfiguration')) instanceof LaunchConfiguration) {
            return $config;
        }
        
        $config = $this->getService()->resource('LaunchConfiguration');
        $config->setParent($this);
        if ($this->getId()) {
            $config->refresh(null, $config->url());
        }
        return $config;
    }
    
    /**
     * NB: NOT SUPPORTED YET.
     * 
     * @codeCoverageIgnore
     */
    public function pause()
    {
        return $this->customAction($this->url('pause', true), 'POST');
    }
    
    /**
     * NB: NOT SUPPORTED YET.
     * 
     * @codeCoverageIgnore
     */
    public function resume()
    {
        return $this->customAction($this->url('resume', true), 'POST');
    }
    
    /**
     * Get the scaling policies associated with this autoscale group.
     * 
     * @return Collection
     */
    public function getPolicies()
    {
        return $this->service()->resourceList('ScalingPolicy', null, $this);
    }
    
    /**
     * Get a particular scaling policy for this autoscale group.
     * 
     * @param  object|int $id
     * @return ScalingPolicy
     */
    public function getPolicy($id = null)
    {
        $config = $this->getService()->resource('ScalingPolicy');
        $config->setParent($this);
        if ($id) {
            $config->populate($id);
        }
        return $config;
    }
    
}