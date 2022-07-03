<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Advertisement;
use Illuminate\Http\Request;

class AdvertisementController extends Controller
{

	public function index(){
		$pageTitle = 'Advertisements';
		$ads = Advertisement::latest()->paginate(getPaginate());
		return view('admin.ad.index', compact('pageTitle', 'ads'));
	}

	public function create(Request $request){

		$request->validate([
			'type' => 'required|in:1,2',
			'url' => 'nullable|url|max:250',
			'image' => 'nullable|image|mimes:jpeg,png,jpg,gif',
			'status' => 'sometimes|in:on'
		]);

		$location = imagePath()['ad']['path'];
		$size = imagePath()['ad']['size'];

		$new = new Advertisement();

		if ($request->type == 1) {
			$new->url = $request->url;
			$new->image = uploadImage($request->image, $location, $size);
		}else{
			$new->script = $request->script;
		}
		$new->type = $request->type;
		$new->status = isset($request->status) ? 1 : 0;
		$new->save();

		$notify[] = ['success', 'Advertisement created successfully.'];
        return back()->withNotify($notify);
	}

	public function update(Request $request){

		$request->validate([
			'id' => 'required|exists:advertisements,id',
			'type' => 'required|in:1,2',
			'url' => 'nullable|url|max:250',
			'image' => 'nullable|image|mimes:jpeg,png,jpg,gif',
			'status' => 'sometimes|in:on'
		]);

		$ad = Advertisement::find($request->id);
		if ($request->type == 1) {
			$ad->url = $request->url;
			if($request->hasFile('image')){
				$location = imagePath()['ad']['path'];
				$size = imagePath()['ad']['size'];
				$ad->image = uploadImage($request->image, $location, $size, $ad->image);
			}

		}else{
			$ad->script = $request->script;
		}

		$ad->status = isset($request->status) ? 1 : 0;

		$ad->save();

		$notify[] = ['success', 'Advertisement updated successfully.'];
        return back()->withNotify($notify);
	}

	public function delete(Request $request){

		$request->validate([
			'id' => 'required|exists:advertisements,id'
		]);

		$location = imagePath()['ad']['path'];

		$ad = Advertisement::find($request->id);
		removeFile($location.'/'.$ad->image);
		$ad->delete();

		$notify[] = ['success', 'Advertisement deleted successfully.'];
        return back()->withNotify($notify);
	}




}
