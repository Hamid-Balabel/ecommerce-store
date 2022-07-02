<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\MainCategoryRequest;
use App\Http\Requests\SubCategoryRequest;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SubCategoriesController extends Controller
{
    public function index(){

        $categories= Category::child()->orderBy('id','DESC')->paginate(PAGINATION_COUNT);
        return view('dashboard.subcategories.index',compact('categories'));
    }

    public function create(){
        $categories= Category::parent()->orderBy('id','DESC')->get();

        return view('dashboard.subcategories.create',compact('categories'));
    }



    public function store(SubCategoryRequest $request){
        try{

            if (!$request->has('is_active'))
                $request->request->add(['is_active'=>0]);
            else
                $request->request->add(['is_active'=>1]);

            $category= Category::create($request->except('_token'));

            //save the translation attribute

            $category->name= $request->name;
            $category->save();


            return redirect()->route('admin.subcategories')->with(['success'=>'تمت الاضافة بنجاح']);


        }catch (\Exception $ex){
            return redirect()->route('admin.maincategories')->with(['error'=>' هناك خطأ بالبينات !!!']);
        }

    }




    public function edit($id){
        $category= Category::orderBy('id','DESC')->find($id);
        if(!$category){
            return redirect()->route('admin.subcategories')->with(['error'=>'__("admin/general.this category is not found")']);
        }
        $categories= Category::parent()->orderBy('id','DESC')->get();

        return view('dashboard.subcategories.edit',compact('category','categories'));
    }


    public function update($id, subCategoryRequest $request){

        try{
            if (!$request->has('is_active'))
                $request->request->add(['is_active'=>0]);
            else
                $request->request->add(['is_active'=>1]);
            $category= Category::find($id);

            if(!$category)
                return redirect()->route('admin.subcategories')->with(['error'=>' لم يتم التحديث هناك خطأ بالبينات !!!']);

            $category->update($request->all());

            //save the translation attribute

            $category->name= $request->name;
            $category->save();

            return redirect()->route('admin.subcategories')->with(['success'=>'تم تحديث البيانات']);

        }catch (\Exception $ex){
            return redirect()->route('admin.subcategories')->with(['error'=>'لم يتم التحديث هناك خطأ بالبينات !!!']);
        }
    }



    public function destroy($id){
        try{
            $category= Category::orderBy('id','DESC')->find($id);
            if(!$category)
                return redirect()->route('admin.subcategories')->with(['error'=>'__("admin/general.this category is not found")']);

            $category->delete();
            return redirect()->route('admin.subcategories')->with(['success'=>'تم الحذف بنجاح']);

        }catch (\Exception $ex){
            return redirect()->route('admin.subcategories')->with(['error'=>' هناك خطأ بالبينات !!!']);
        }
    }
}
