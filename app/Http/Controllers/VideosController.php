<?php

namespace App\Http\Controllers;

use App\Models\Video;
use Illuminate\Http\Request;

class VideosController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $videos = Video::paginate(10);
        return view('videos.index', compact('videos'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Specify the videos form's rules.
     *
     * @return array
     */
    private function rules()
    {
        return [
            'title' => 'required|string',
            'url' => 'required|string|active_url',
            'photo' => 'sometimes|nullable|image',
        ];
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = $request->validate($this->rules());
        $delimeter = strpos('=', $data['url']) === false ? '/' : '=';
        $data['url'] = substr(strrchr($data['url'], $delimeter), 1);

        Video::create($data);
        $success = 'تمت الااضافة بنجاح';

        if (trim(\Route::current()->getPrefix(), '/') == 'api') {
            return compact('success');
        }
        return back()->with(compact('success'));
    }

    /**
     * Display the specified resource.
     *
     * @param  Video  $video
     * @return \Illuminate\Http\Response
     */
    public function show(Video $video)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  Video  $video
     * @return \Illuminate\Http\Response
     */
    public function edit(Video $video)
    {
        return $video;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  Video  $video
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Video $video)
    {
        $data = $request->validate($this->rules());
        $delimeter = strpos($data['url'], '=') === false ? '/' : '=';
        $data['url'] = substr(strrchr($data['url'], $delimeter), 1);

        $video->update($data);
        $success = 'تم التحديث بنجاح';

        if (trim(\Route::current()->getPrefix(), '/') == 'api') {
            return compact('success');
        }
        return back()->with(compact('success'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Video  $video
     * @return \Illuminate\Http\Response
     */
    public function destroy(Video $video)
    {
        $video->delete();
        $success = 'تم الحذف بنجاح';

        if (trim(\Route::current()->getPrefix(), '/') == 'api') {
            return compact('success');
        }
        return back()->with(compact('success'));
    }
}
