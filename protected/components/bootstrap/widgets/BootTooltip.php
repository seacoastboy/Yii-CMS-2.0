<?php
/**
 * BootTwipsy class file.
 * @author Christoffer Niska <ChristofferNiska@gmail.com>
 * @copyright Copyright &copy; Christoffer Niska 2011-
 * @license http://www.opensource.org/licenses/bsd-license.php New BSD License
 */

Yii::import('bootstrap.widgets.BootWidget');

class BootTooltip extends BootWidget
{
	/**
	 * @var string the CSS selector to use for selecting the twipsy elements.
	 */
	public $selector = 'a[rel="tooltip"]';
	
	/**
	 * Initializes the widget.
	 */
	public function init()
	{
		parent::init();
		$this->registerScriptFile('jquery.ui.boot-tooltip.js');
	}

	/**
	 * Runs the widget.
	 */
	public function run()
	{
		$id = $this->getId();
		$options = !empty($this->options) ? CJavaScript::encode($this->options) : '';
		$this->registerScript(__CLASS__.'#'.$id,"jQuery('{$this->selector}').bootTooltip($options);");
	}
}