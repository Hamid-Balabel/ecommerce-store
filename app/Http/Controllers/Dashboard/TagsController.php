<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\AttributeRequest;
use App\Http\Requests\TagRequest;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TagsController extends Controller
{
    public function index(){

        $tags= Tag::orderBy('id','DESC')->paginate(PAGINATION_COUNT);
        return view('dashboard.tags.index',compact('tags'));
    }

    public function create(){

        return view('dashboard.tags.create');
    }



    public function store(TagRequest $request){
            DB::beginTransaction();

            $tag = Tag::create(['slug'=>$request->slug]);

            //save the translation attribute

            $tag->name = $request->name;
            $tag->save();

            DB::commit();

            return redirect()->route('admin.tags')->with(['success' => 'تمت الاضافة بنجاح']);

    }



    public function edit($id){
        $tag= Tag::find($id);
        if(!$tag){
            return redirect()->route('admin.tags')->with(['error'=>'__("admin/general.this brand is not found")']);
        }
        return view('dashboard.tags.edit',compact('tag'));
    }


    public function update($id, TagRequest $request){

//        try{
            DB::beginTransaction();

            $tag= Tag::find($id);

            if(!$tag)
                return redirect()->route('admin.tags')->with(['error'=>' لم يتم التحديث هناك خطأ بالبينات !!!']);


            $tag->update(['slug'=>$request->slug]);

            //save the translation attribute

            $tag->name = $request->name ;
            $tag->save();

            DB::commit();

            return redirect()->route('admin.tags')->with(['success'=>'تم تحديث البيانات']);

//        }catch (\Exception $ex){
//            DB::rollBack();
//            return redirect()->route('admin.tags')->with(['error'=>'لم يتم التحديث هناك خطأ بالبينات !!!']);
//        }
    }



    public function destroy($id){
        try{
            $tag= Tag::find($id);
            if(!$tag)
                return redirect()->route('admin.tags')->with(['error'=>'__("admin/general.this brand is not found")']);

            $tag->delete();
            return redirect()->route('admin.tags')->with(['success'=>'تم الحذف بنجاح']);

        }catch (\Exception $ex){
            return redirect()->route('admin.tags')->with(['error'=>' هناك خطأ بالبينات !!!']);
        }
    }
}
