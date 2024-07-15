<?php

use Bitrix\Main\EventManager;
use Bitrix\Main\Page\Asset;

defined('B_PROLOG_INCLUDED') || die();

/**
 * Final class AddTaskToIt
 */
final class AddTaskToIt
{
    private const DS             = DIRECTORY_SEPARATOR;
    private const MAIN_DIR       = __DIR__;
    private const LOCAL_MAIN_DIR = self::DS . 'local' . self::DS . 'php_interface' . self::DS . 'addTaskToIt';
    private const TEMPLATE_DIR   = self::MAIN_DIR . self::DS . 'templates';
    private const CSS_DIR        = self::LOCAL_MAIN_DIR . self::DS . 'css';
    private const JS_DIR         = self::LOCAL_MAIN_DIR . self::DS . 'js';

    private Asset $asset;
    private int   $user_id;

    /**
     * Run main logic
     */
    public function run(): void
    {
        if ($this->isAjax()) {
            return;
        }

        $this->setProperty();
        $this->loadJsAndCss();
        $this->setHtmlBlock();
        $this->addHtmlBlock();
    }

    /**
     * Check is http ajax
     *
     * @return bool
     */
    private function isAjax(): bool
    {
        if ($_SERVER['HTTP_BX_AJAX'] !== null || strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
            return true;
        }

        return false;
    }

    /**
     * Set property class
     */
    private function setProperty(): void
    {
        global $USER;
        $this->asset   = Asset::getInstance();
        $this->user_id = (int)$USER->GetID();
    }

    /**
     * Load js and css
     */
    private function loadJsAndCss(): void
    {
        CJSCore::RegisterExt(
            'addTaskToIt',
            [
                'js'  => self::JS_DIR . self::DS . 'addTaskToIt.js',
                'css' => self::CSS_DIR . self::DS . 'addTaskToIt.css'
            ]
        );

        CJSCore::init('addTaskToIt');
    }

    /**
     * Set html block
     */
    private function setHtmlBlock(): void
    {
        $template = addslashes($this->minify($this->getTemplate('block')));
        $this->asset->addString(
            '<script type="text/javascript">BX.ready(function () { BX.addTaskToIt.setHtmlBlock("' . $template . '");});</script>'
        );
    }

    /**
     * Set String in line
     *
     * @param string $str
     *
     * @return string
     */
    private function minify(string $str): string
    {
        return (string)str_replace(["\r\n", "\n"], '', $str);
    }

    /**
     * Get html template
     *
     * @param string $template
     *
     * @return string
     */
    private function getTemplate(string $template): string
    {
        $file = self::TEMPLATE_DIR . self::DS . $template . '.php';

        if ( ! file_exists($file)) {
            return '';
        }

        ob_start();
        include_once $file;
        $content = ob_get_contents();
        ob_end_clean();

        return (string)$content;
    }

    /**
     * Add html block
     */
    private function addHtmlBlock(): void
    {
        $this->asset->addString(
            '<script type="text/javascript">BX.ready(function () { BX.addTaskToIt.addHtmlBlock(); });</script>'
        );
    }
}

$eventManager = EventManager::getInstance();
$eventManager->addEventHandler('main', 'OnBeforeProlog', [new AddTaskToIt(), 'run']);