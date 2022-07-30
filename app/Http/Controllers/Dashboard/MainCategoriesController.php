<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\MainCategoryRequest;
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
        return view('dashboard.categories.create',compact('categories'));
    }



    public function store(MainCategoryRequest $request){

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




    }




    public function edit($id){
        $category= Category::orderBy('id','DESC')->find($id);
        if(!$category){
            return redirect()->route('admin.maincategories')->with(['error'=>'__("admin/general.this category is not found")']);
        }
        return view('dashboard.categories.edit',compact('category'));
    }


    public function update ($id, MainCategoryRequest $request)
    {
        try {

            $category = Category::find($id);

            if (!$category)
                return redirect()->route('admin.maincategories')->with(['error' => 'هذا القسم غير موجود']);

            if (!$request->has('is_active'))
                $request->request->add(['is_active' => 0]);
            else
                $request->request->add(['is_active' => 1]);

            $category->update($request->all());

            $category->name = $request->name;
            $category->save();

            return redirect()->route('admin.maincategories')->with(['success' => 'تم ألتحديث بنجاح']);
        } catch (\Exception $ex) {

            return redirect()->route('admin.maincategories')->with(['error' => 'حدث خطا ما برجاء المحاوله لاحقا']);
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
