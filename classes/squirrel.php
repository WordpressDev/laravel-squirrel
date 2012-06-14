<?php

/**
 * Squirrel Core
 *
 * @package squirrel
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
	public static function register($object, Closure $nut)
	{
		// create a new nut object
		$n = new Nut();

		// set the object name
		$n->object = $object;

		// set the resource names to the lower of
		// the object by default
		$n->singular = Str::lower($object);
		$n->plural = Str::plural(Str::lower($object));

		// hand the nut to the route closure
		call_user_func($nut, $n);

		// handle GET requests
		static::handle_get($n);
		// handle POST requests
		static::handle_post($n);		
	}

	/**
	 * Handle GET requests for registered Squirrel objects.
	 * @param  Nut $nut  The nut for the GET request.
	 * @return string Description of the object.
	 */
	private static function handle_get($nut)
	{
		// make sure we allow get requests
		if(! $nut->get) return;

		// listing of objects
		Route::get($nut->stub.'/'.$nut->plural, function() use ($nut) {

			// get the class name
			$class = $nut->object;

			// retrieve all objects
			$object = $class::get();

			// return the JSON formatted array of objects
			return Response::json($object);
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
			return Response::json($object);
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
		if(! $nut->post) return;

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
			return Response::json($object);
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
		if(! $nut->put) return;

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
			return Response::json($object);
		});			
	}

	/**
	 * Handle DELETE requests for registered Squirrel objects.
	 * @param  Nut $nut  The nut for the request.
	 * @return string Description of the object.
	 */
	private static function handle_delete($nut)
	{
		// make sure we allow put requests
		if(! $nut->put) return;

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