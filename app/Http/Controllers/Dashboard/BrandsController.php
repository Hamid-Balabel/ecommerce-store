<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\BrandRequest;
use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BrandsController extends Controller
{
    public function index(){

        $brands= Brand::orderBy('id','DESC')->paginate(PAGINATION_COUNT);
        return view('dashboard.brands.index',compact('brands'));
    }

    public function create(){

        return view('dashboard.brands.create');
    }



    public function store(BrandRequest $request){


            if (!$request->has('is_active'))
                $request->request->add(['is_active'=>0]);
            else
                $request->request->add(['is_active'=>1]);

            $filename='';
            if($request->has('photo')){
                $filename= uploadImage('brands', $request->photo);
            }

            $brand= Brand::create($request->except('_token','photo'));

            //save the translation attribute

            $brand->name= $request->name;
            $brand->photo= $filename;
            $brand->save();



            return redirect()->route('admin.brands')->with(['success'=>'تمت الاضافة بنجاح']);




    }




    public function edit($id){
        $category= Category::orderBy('id','DESC')->find($id);
        if(!$category){
            return redirect()->route('admin.maincategories')->with(['error'=>'__("admin/general.this category is not found")']);
        }
        return view('dashboard.categories.edit',compact('category'));
    }


    public function update($id, MainCategoryRequest $request){

        try{
            if (!$request->has('is_active'))
                $request->request->add(['is_active'=>0]);
            else
                $request->request->add(['is_active'=>1]);
            $category= Category::find($id);

            if(!$category)
                return redirect()->route('admin.maincategories')->with(['error'=>' لم يتم التحديث هناك خطأ بالبينات !!!']);

            $category->update($request->all());

            //save the translation attribute

            $category->name= $request->name;
            $category->save();

            return redirect()->route('admin.maincategories')->with(['success'=>'تم تحديث البيانات']);

        }catch (\Exception $ex){
            return redirect()->route('admin.maincategories')->with(['error'=>'لم يتم التحديث هناك خطأ بالبينات !!!']);
        }
    }



    public function destroy($id){
        try{
            $category= Category::orderBy('id','DESC')->find($id);
            if(!$category)
                return redirect()->route('admin.maincategories')->with(['error'=>'__("admin/general.this category is not found")']);

            $category->delete();
            return redirect()->route('admin.maincategories')->with(['success'=>'تم الحذف بنجاح']);

        }catch (\Exception $ex){
            return redirect()->route('admin.maincategories')->with(['error'=>' هناك خطأ بالبينات !!!']);
        }
    }
}
