<?php

namespace app\components;

use yii\base\BootstrapInterface;
use yii\base\Application;
use app\models\Setting;

/**
 * Settings Bootstrap Component
 */
class SettingsBootstrap implements BootstrapInterface
{
    /**
     * {@inheritdoc}
     */
    public function bootstrap($app)
    {
        try {
            // Check if settings table exists and initialize defaults
            Setting::initializeDefaults();
        } catch (\Exception $e) {
            // If settings table doesn't exist yet, skip initialization
            // This will happen during migration
        }
    }
}
