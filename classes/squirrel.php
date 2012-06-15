<?php

/**
 * Squirrel Core
 *
 * @package squirrel
 * @version  1.0
 * @license MIT License <http://www.opensource.org/licenses/mit-license.php>
 * @copyright Dayle Rees 2012.
 * @author Dayle Rees <me@daylerees.com>
 */
class Squirrel
{
	/**
	 * Register a new object with Squirrel
	 * @param  string  $object An Eloquent Model name.
	 * @param  Closure $nut    Handle object settings.
	 * @return void
	 */
	public static function register($type, Closure $object = null)
	{
		// create a new nut object
		$nut = new Nut();

		// set the object name
		$nut->object = $type;

		// set the resource names to the lower of
		// the object by default
		$nut->singular 	= Str::lower($type);
		$nut->plural 	= Str::plural(Str::lower($type));

		// hand the nut to the route closure
		if ($object != null) call_user_func($object, $nut);

		// request types to process
		$requests = array('get', 'put', 'post', 'delete');

		// loop each request
		foreach ($requests as $request)
		{
			// create routes
			$method = 'handle_'.$request;
			static::$method($nut);
		}
	}

	/**
	 * Handle GET requests for registered Squirrel objects.
	 * @param  Nut $nut  The nut for the GET request.
	 * @return string Description of the object.
	 */
	private static function handle_get($nut)
	{
		// make sure we allow get requests
		if (! $nut->get)
			return Response::make('GET requests to this resource are not allowed.', 403);

		// listing of objects
		Route::get($nut->stub.'/'.$nut->plural, function() use ($nut) {

			// get the class name
			$class = $nut->object;

			// retrieve all objects
			$objects = $class::get();

			// array to store data
			$all = array();

			// loop object + add to all
			foreach($objects as $object)
			{
				$all[] = $object->attributes;
			}

			// return the JSON formatted array of objects
			return Response::json($all);
		});

		// request a single object
		Route::get($nut->stub.'/'.$nut->singular.'/(:num)', function($id) use ($nut) {

			// get class name
			$class = $nut->object;

			// find the object requested
			$object = $class::find($id);

			// it doesn't exist? 404
			if (! $object) return Response::error('404');

			// return the object in json format
			return Response::json($object->attributes);
		});
	}

	/**
	 * Handle POST requests for registered Squirrel objects.
	 * @param  Nut $nut  The nut for the request.
	 * @return string Description of the object.
	 */
	private static function handle_post($nut)
	{
		// make sure we allow post requests
		if (! $nut->post)
			return Response::make('POST requests to this resource are not allowed.', 403);

		// create a new object
		Route::post($nut->stub.'/'.$nut->singular, function() use ($nut) {

			// get class name
			$class = $nut->object;

			// make a new object of this type
			$object = new $class();

			// fill with post data
			$object->fill(Input::get());

			// save the object
			$object->save();

			// return the object in json format
			return Response::json($object->attributes);
		});
	}

	/**
	 * Handle PUT requests for registered Squirrel objects.
	 * @param  Nut $nut  The nut for the request.
	 * @return string Description of the object.
	 */
	private static function handle_put($nut)
	{
		// make sure we allow put requests
		if (! $nut->put)
			return Response::make('PUT requests to this resource are not allowed.', 403);

		// update a single object
		Route::put($nut->stub.'/'.$nut->singular.'/(:num)', function($id) use ($nut) {

			// get class name
			$class = $nut->object;

			// find the object requested
			$object = $class::find($id);

			// it doesn't exist? 404
			if (! $object) return Response::error('404');

			// fill with post data
			$object->fill(Input::get());

			// save the object
			$object->save();

			// return the object in json format
			return Response::json($object->attributes);
		});
	}

	/**
	 * Handle DELETE requests for registered Squirrel objects.
	 * @param  Nut $nut  The nut for the request.
	 * @return string Delete confirmation.
	 */
	private static function handle_delete($nut)
	{
		// make sure we allow put requests
		if (! $nut->put)
			return Response::make('DELETE requests to this resource are not allowed.', 403);

		// delete a single object
		Route::delete($nut->stub.'/'.$nut->singular.'/(:num)', function($id) use ($nut) {

			// get class name
			$class = $nut->object;

			// find the object requested
			$object = $class::find($id);

			// it doesn't exist? 404
			if (! $object) return Response::error('404');

			// delete the object
			$object->delete();

			// show some kind of confirmation
			return Response::make('deleted', 200);
		});
	}
}
