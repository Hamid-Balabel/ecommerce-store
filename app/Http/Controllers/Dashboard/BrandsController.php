<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\AttributeRequest;
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



    public function store(AttributeRequest $request){
        try {
            DB::beginTransaction();

            if (!$request->has('is_active'))
                $request->request->add(['is_active' => 0]);
            else
                $request->request->add(['is_active' => 1]);

            $filename = '';
            if ($request->has('photo')) {
                $filename = uploadImage('brands', $request->photo);
            }

            $brand = Brand::create($request->except('_token', 'photo'));

            //save the translation attribute

            $brand->name = $request->name;
            $brand->photo = $filename;
            $brand->save();

            DB::commit();

            return redirect()->route('admin.brands')->with(['success' => 'تمت الاضافة بنجاح']);
        } catch (\Exception $ex) {
            DB::rollBack();
            return redirect()->route('admin.brands')->with(['error' => ' هناك خطأ بالبينات !!!']);
        }



    }




    public function edit($id){
        $brand= Brand::find($id);
        if(!$brand){
            return redirect()->route('admin.brands')->with(['error'=>'__("admin/general.this brand is not found")']);
        }
        return view('dashboard.brands.edit',compact('brand'));
    }


    public function update($id, AttributeRequest $request){

        try{
            DB::beginTransaction();
            if (!$request->has('is_active'))
                $request->request->add(['is_active'=>0]);
            else
                $request->request->add(['is_active'=>1]);
            $brand= Brand::find($id);

            if(!$brand)
                return redirect()->route('admin.brands')->with(['error'=>' لم يتم التحديث هناك خطأ بالبينات !!!']);

            if($request->has('photo')){
                $filename=uploadImage('brands',$request->photo);
                Brand::where('id',$id)->update(['photo'=>$filename]);
            }

            $brand->update($request->except('_token','id','photo'));

            //save the translation attribute

            $brand->name= $request->name;
            $brand->save();

            DB::commit();

            return redirect()->route('admin.brands')->with(['success'=>'تم تحديث البيانات']);

        }catch (\Exception $ex){
            DB::rollBack();
            return redirect()->route('admin.brands')->with(['error'=>'لم يتم التحديث هناك خطأ بالبينات !!!']);
        }
    }



    public function destroy($id){
        try{
            $brand= Brand::find($id);
            if(!$brand)
                return redirect()->route('admin.brands')->with(['error'=>'__("admin/general.this brand is not found")']);

            $brand->delete();
            return redirect()->route('admin.brands')->with(['success'=>'تم الحذف بنجاح']);

        }catch (\Exception $ex){
            return redirect()->route('admin.brands')->with(['error'=>' هناك خطأ بالبينات !!!']);
        }
    }
}
