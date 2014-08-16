<?php
namespace Hyperion\ApiBundle\Form;

use Symfony\Component\Form\AbstractType;

abstract class WebApiType extends AbstractType
{

    protected $web_mode = false;

    /**
     * Set WebMode
     *
     * @param boolean $web_mode
     * @return $this
     */
    public function setWebMode($web_mode)
    {
        $this->web_mode = (bool)$web_mode;
        return $this;
    }

    /**
     * Get WebMode
     *
     * @return boolean
     */
    public function isWebMode()
    {
        return $this->web_mode;
    }

}
