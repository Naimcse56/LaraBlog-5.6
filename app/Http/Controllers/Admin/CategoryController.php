<?php

namespace App\Http\Controllers\Admin;

use App\Category;
use Brian2694\Toastr\Facades\Toastr;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $categories = Category::latest()->get();
        return view('admin.category.index',compact('categories'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.category.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request,[
            'name' => 'required|unique:categories',
            'image' => 'required|mimes:jpeg,png,jpg'
        ]);
        // get form image
        $image = $request->file('image');
        $slug = str_slug($request->name);
        if (isset($image))
        {
//            make unique name for image
            $currentDate = Carbon::now()->toDateString();
            $imagename = $slug.'-'.$currentDate.'-'.uniqid().'.'.$image->getClientOriginalExtension();
//            check category dir is exists
            if (!Storage::disk('public')->exists('naimcategory'))
            {
                Storage::disk('public')->makeDirectory('naimcategory');
            }
//            resize image for category and upload
            $category = Image::make($image)->resize(1600,479)->stream(); // stream() is used here to save data in $category
            Storage::disk('public')->put('naimcategory/'.$imagename,$category);

            //            check category slider dir is exists
            if (!Storage::disk('public')->exists('naimcategory/slider'))
            {
                Storage::disk('public')->makeDirectory('naimcategory/slider');
            }
            //            resize image for category slider and upload
            $slider = Image::make($image)->resize(500,333)->stream();
            Storage::disk('public')->put('naimcategory/slider/'.$imagename,$slider);

        } else {
            $imagename = "default.png";
        }

        $category = new Category();
        $category->name = $request->name;
        $category->slug = $slug;
        $category->image = $imagename;
        $category->save();
        Toastr::success('Category Successfully Added :)' ,'Success');
        return redirect()->route('admin.category.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $category = Category::find($id);
        return view('admin.category.edit',compact('category'));
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
        $this->validate($request,[
            'name' => 'required',
            'image' => 'mimes:jpeg,bmp,png,jpg'
        ]);
        // get form image
        $image = $request->file('image');
        $slug = str_slug($request->name);
        $category = Category::find($id);
        if (isset($image))
        {
//            make unique name for image
            $currentDate = Carbon::now()->toDateString();
            $imagename = $slug.'-'.$currentDate.'-'.uniqid().'.'.$image->getClientOriginalExtension();
//            check category dir is exists
            if (!Storage::disk('public')->exists('naimcategory'))
            {
                Storage::disk('public')->makeDirectory('naimcategory');
            }
//            delete old image
            if (Storage::disk('public')->exists('naimcategory/'.$category->image))
            {
                Storage::disk('public')->delete('naimcategory/'.$category->image);
            }
//            resize image for category and upload
            $categoryimage = Image::make($image)->resize(1600,479)->stream();
            Storage::disk('public')->put('naimcategory/'.$imagename,$categoryimage);

            //            check category slider dir is exists
            if (!Storage::disk('public')->exists('naimcategory/slider'))
            {
                Storage::disk('public')->makeDirectory('naimcategory/slider');
            }
            //            delete old slider image
            if (Storage::disk('public')->exists('naimcategory/slider/'.$category->image))
            {
                Storage::disk('public')->delete('naimcategory/slider/'.$category->image);
            }
            //            resize image for category slider and upload
            $slider = Image::make($image)->resize(500,333)->stream();
            Storage::disk('public')->put('naimcategory/slider/'.$imagename,$slider);

        } else {
            $imagename = $category->image;
        }

        $category->name = $request->name;
        $category->slug = $slug;
        $category->image = $imagename;
        $category->save();
        Toastr::success('Category Successfully Updated :)' ,'Success');
        return redirect()->route('admin.category.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $category = Category::find($id);
        if (Storage::disk('public')->exists('naimcategory/'.$category->image))
        {
            Storage::disk('public')->delete('naimcategory/'.$category->image);
        }

        if (Storage::disk('public')->exists('naimcategory/slider/'.$category->image))
        {
            Storage::disk('public')->delete('naimcategory/slider/'.$category->image);
        }
        $category->delete();
        Toastr::success('Category Successfully Deleted :)','Success');
        return redirect()->back();
    }
    
}
