<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\TagRequest;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MainCategoriesController extends Controller
{
    public function index(){

        $categories = Category::with('_parent')->orderBy('id','DESC') -> paginate(PAGINATION_COUNT);
        return view('dashboard.categories.index', compact('categories'));
    }

    public function create(){

        $categories =   Category::select('id','parent_id')->get();
        return view('dashboard.categories.create',compact('categories'));    }



    public function store(TagRequest $request){
        try{
            DB::beginTransaction();

            if (!$request->has('is_active'))
                $request->request->add(['is_active'=>0]);
            else
                $request->request->add(['is_active'=>1]);

            if($request->type ==1){
                $request->request->add(['parent_id'=>null]);
            }

            $category= Category::create($request->except('_token'));

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
        $category= Category::orderBy('id','DESC')->find($id);
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
