<?php

/**
 * A nut represents an object that can be used by
 * the Squirrel API builder.
 *
 * @package squirrel
 * @version  1.0
 * @license MIT License <http://www.opensource.org/licenses/mit-license.php>
 * @copyright Dayle Rees 2012.
 * @author Dayle Rees <me@daylerees.com>
 */
class Nut
{

	/**
	 * Set the name of the object to handle.
	 * @var string
	 */
	public $object = null;

	/**
	 * The singular name of the resource as it appears in
	 * the URI.
	 * @var string
	 */
	public $singular = null;

	/**
	 * The plural name of the resource as it appear in
	 * the URI.
	 * @var string
	 */
	public $plural = null;

	/**
	 * The stub for the api, uses :bundle by default.
	 * @var string
	 */
	public $stub = '(:bundle)';

	/**
	 * Allow GET methods on this object.
	 * @var boolean
	 */
	public $get = true;

	/**
	 * Allow PUT methods on this object.
	 * @var boolean
	 */
	public $put = true;

	/**
	 * Allow POST methods on this object.
	 * @var boolean
	 */
	public $post = true;

	/**
	 * Allow DELETE methods on this object.
	 * @var boolean
	 */
	public $delete = true;

}
