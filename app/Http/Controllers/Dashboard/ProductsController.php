<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\GenralProductRequest;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductsController extends Controller
{
    public function index(){


    }

    public function create(){
        $data=[];
        $data['brands']= Brand::active()->select('id')->get();
        $data['tags']= Tag::select('id')->get();
        $data['categories']= Category::active()->select('id')->get();
        return view('dashboard.products.general.create',$data);
    }



    public function store(GenralProductRequest $request){
        try{
            DB::beginTransaction();

            if (!$request->has('is_active'))
                $request->request->add(['is_active'=>0]);
            else
                $request->request->add(['is_active'=>1]);

            if($request->type ==1){
                $request->request->add(['parent_id'=>null]);
            }

            $category= Product::create($request->except('_token'));

            //save the translation attribute

            $category->name= $request->name;
            $category->save();


            DB::commit();
            return redirect()->route('admin.maincategories')->with(['success'=>'تمت الاضافة بنجاح']);


        }catch (\Exception $ex){
            DB::rollBack();
            return redirect()->route('admin.maincategories')->with(['error'=>' هناك خطأ بالبينات !!!']);
        }

    }




    public function edit($id){
        $category= Product::orderBy('id','DESC')->find($id);
        if(!$category){
            return redirect()->route('admin.maincategories')->with(['error'=>'__("admin/general.this category is not found")']);
        }
        return view('dashboard.categories.edit',compact('category'));
    }


    public function update($id, TagRequest $request){

        try{
            if (!$request->has('is_active'))
                $request->request->add(['is_active'=>0]);
            else
                $request->request->add(['is_active'=>1]);
            $category= Product::find($id);

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
            $category= Product::orderBy('id','DESC')->find($id);
            if(!$category)
                return redirect()->route('admin.maincategories')->with(['error'=>'__("admin/general.this category is not found")']);

            $category->delete();
            return redirect()->route('admin.maincategories')->with(['success'=>'تم الحذف بنجاح']);

        }catch (\Exception $ex){
            return redirect()->route('admin.maincategories')->with(['error'=>' هناك خطأ بالبينات !!!']);
        }
    }
}
