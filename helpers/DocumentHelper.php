<?php

namespace app\helpers;

use app\models\Setting;
use DateTime;

/**
 * Document Number Helper
 */
class DocumentHelper
{
    /**
     * Generate next document number
     * @param string $type (quotation, invoice, receipt)
     * @return string
     */
    public static function generateDocumentNumber($type)
    {
        $prefix = Setting::get($type . '_prefix', strtoupper(substr($type, 0, 3)));
        $format = Setting::get($type . '_format', '{prefix}{year}{month}-{number:4}');
        $counter = (int) Setting::get($type . '_counter', 1);
        
        $now = new DateTime();
        
        // Replace placeholders
        $number = str_replace([
            '{prefix}',
            '{year}',
            '{month}',
            '{day}',
        ], [
            $prefix,
            $now->format('Y'),
            $now->format('m'),
            $now->format('d'),
        ], $format);
        
        // Handle {number:X} pattern
        if (preg_match('/\{number:(\d+)\}/', $number, $matches)) {
            $padding = (int) $matches[1];
            $formattedNumber = str_pad($counter, $padding, '0', STR_PAD_LEFT);
            $number = str_replace($matches[0], $formattedNumber, $number);
        } else {
            $number = str_replace('{number}', $counter, $number);
        }
        
        // Increment counter for next use
        Setting::set($type . '_counter', $counter + 1);
        
        return $number;
    }

    /**
     * Preview document number format
     * @param string $type
     * @param string $format
     * @param string $prefix
     * @param int $counter
     * @return string
     */
    public static function previewDocumentNumber($type, $format, $prefix, $counter = 1)
    {
        $now = new DateTime();
        
        // Replace placeholders
        $number = str_replace([
            '{prefix}',
            '{year}',
            '{month}',
            '{day}',
        ], [
            $prefix,
            $now->format('Y'),
            $now->format('m'),
            $now->format('d'),
        ], $format);
        
        // Handle {number:X} pattern
        if (preg_match('/\{number:(\d+)\}/', $number, $matches)) {
            $padding = (int) $matches[1];
            $formattedNumber = str_pad($counter, $padding, '0', STR_PAD_LEFT);
            $number = str_replace($matches[0], $formattedNumber, $number);
        } else {
            $number = str_replace('{number}', $counter, $number);
        }
        
        return $number;
    }

    /**
     * Calculate VAT amount
     * @param float $amount
     * @param float $vatRate
     * @return float
     */
    public static function calculateVAT($amount, $vatRate = null)
    {
        if ($vatRate === null) {
            $vatRate = (float) Setting::get('vat_rate', 7);
        }
        
        return $amount * ($vatRate / 100);
    }

    /**
     * Calculate WHT amount
     * @param float $amount
     * @param float $whtRate
     * @return float
     */
    public static function calculateWHT($amount, $whtRate = null)
    {
        if ($whtRate === null) {
            $whtRate = (float) Setting::get('wht_rate', 3);
        }
        
        return $amount * ($whtRate / 100);
    }

    /**
     * Check if VAT is enabled
     * @return bool
     */
    public static function isVATEnabled()
    {
        return (bool) Setting::get('vat_enabled', true);
    }

    /**
     * Check if WHT is enabled
     * @return bool
     */
    public static function isWHTEnabled()
    {
        return (bool) Setting::get('wht_enabled', true);
    }

    /**
     * Get company information
     * @return array
     */
    public static function getCompanyInfo()
    {
        return [
            'name' => Setting::get('company_name', 'CleaningWell Co., Ltd.'),
            'address' => Setting::get('company_address', ''),
            'tax_id' => Setting::get('company_tax_id', ''),
            'phone' => Setting::get('company_phone', ''),
            'email' => Setting::get('company_email', ''),
            'logo' => Setting::get('company_logo', ''),
        ];
    }
}
