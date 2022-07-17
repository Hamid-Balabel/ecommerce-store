<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\AttributeRequest;
use App\Models\Attribute;
use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AttributesController extends Controller
{
    public function index(){

        $attributes= Attribute::orderBy('id','DESC')->paginate(PAGINATION_COUNT);
        return view('dashboard.attributes.index',compact('attributes'));
    }

    public function create(){

        return view('dashboard.attributes.create');
    }



    public function store(AttributeRequest $request){
        try {
            DB::beginTransaction();

            $attribute=Attribute::create([]);

            //save the translation attribute

            $attribute->name = $request->name;
            $attribute->save();

            DB::commit();

            return redirect()->route('admin.attributes')->with(['success' => 'تمت الاضافة بنجاح']);
        } catch (\Exception $ex) {
            DB::rollBack();
            return redirect()->route('admin.attributes')->with(['error' => ' هناك خطأ بالبينات !!!']);
        }

    }




    public function edit($id){
        $attribute= Attribute::find($id);
        if(!$attribute){
            return redirect()->route('admin.attributes')->with(['error'=>'__("admin/general.this attribute is not found")']);
        }
        return view('dashboard.attributes.edit',compact('attribute'));
    }



    public function update($id, AttributeRequest $request){

        try{
            DB::beginTransaction();

            $attribute= Attribute::find($id);

            if(!$attribute)
                return redirect()->route('admin.attributes')->with(['error'=>' لم يتم التحديث هناك خطأ بالبينات !!!']);

            //save the translation attribute

            $attribute->name= $request->name;
            $attribute->save();

            DB::commit();

            return redirect()->route('admin.attributes')->with(['success'=>'تم تحديث البيانات']);

        }catch (\Exception $ex){
            DB::rollBack();
            return redirect()->route('admin.attributes')->with(['error'=>'لم يتم التحديث هناك خطأ بالبينات !!!']);
        }
    }



    public function destroy($id){
        try{
            $attribute= Attribute::find($id);
            if(!$attribute)
                return redirect()->route('admin.attributes')->with(['error'=>'__("admin/general.this attribute is not found")']);

            $attribute->delete();
            return redirect()->route('admin.attributes')->with(['success'=>'تم الحذف بنجاح']);

        }catch (\Exception $ex){
            return redirect()->route('admin.attributes')->with(['error'=>' هناك خطأ بالبينات !!!']);
        }
    }
}
