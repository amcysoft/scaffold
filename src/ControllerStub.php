<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\{{capSingle}};

class {{capPlural}}Controller extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        ${{smallPlural}} = {{capSingle}}::paginate(10);
        return view('{{smallPlural}}.index', compact('{{smallPlural}}'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('{{smallPlural}}.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        {{capSingle}}::create($request->all());
        flash()->success(trans('scaffold::messages.create_success', '{{capSingle}}'));
        return redirect('{{smallPlural}}');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        ${{smallSingle}} = {{capSingle}}::findOrFail($id);
        return view('{{smallPlural}}.show', compact('{{smallSingle}}'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        ${{smallSingle}} = {{capSingle}}::findOrFail($id);
        return view('{{smallPlural}}.edit', compact('{{smallSingle}}'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        ${{smallSingle}} = {{capSingle}}::findOrFail($id);
        ${{smallSingle}}->update($request->all());
        flash()->success(trans('scaffold::messages.update_success', '{{capSingle}}'));
        return redirect('{{smallPlural}}');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        ${{smallSingle}} = {{capSingle}}::findOrFail($id);
        ${{smallSingle}}->delete();
        flash()->success(trans('scaffold::messages.delete_success', '{{capSingle}}'));
        return redirect('{{smallPlural}}');
    }
}
