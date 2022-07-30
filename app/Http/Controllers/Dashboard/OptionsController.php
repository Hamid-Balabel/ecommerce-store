<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\GenralProductRequest;
use App\Http\Requests\OptionsRequest;
use App\Http\Requests\ProductPriceValidation;
use App\Http\Requests\ProductImagesRequest;
use App\Models\Attribute;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Image;
use App\Models\Option;
use App\Models\Product;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OptionsController extends Controller
{
    public function index()
    {
        $options = Option::with(['product' => function ($prod) {
            $prod->select('id');
        }, 'attribute' => function ($attr) {
            $attr->select('id');
        }])->select('id', 'product_id', 'attribute_id', 'price')->paginate(PAGINATION_COUNT);

        return view('dashboard.options.index', compact('options'));
    }

    public function create(){
        $data=[];
        $data['products']= Product::active()->select('id')->get();
        $data['attributes']= Attribute::select('id')->get();
        return view('dashboard.options.create',$data);
    }



    public function store(OptionsRequest $request){
        try{
            DB::beginTransaction();

            $option= Option::create([
                'attribute_id'=>$request->attribute_id,
                'product_id'=>$request->product_id,
                'price'=>$request->price,
            ]);

            //save the translation attribute

            $option->name= $request->name;
            $option->save();

            // save product categories

            DB::commit();
            return redirect()->route('admin.options')->with(['success'=>'تمت الاضافة بنجاح']);

        }catch (\Exception $ex){
            DB::rollBack();
            return redirect()->route('admin.options')->with(['error'=>' هناك خطأ بالبينات !!!']);
        }

    }


    public function edit($id){
        $data=[];
        $data['option']= Option::find($id);
        if (!$data['option'])
            return redirect()->route('admin.options')->with(['error' => '__("admin/general.this option is not found")']);

        $data['products']= Product::active()->select('id')->get();
        $data['attributes']= Attribute::select('id')->get();
        return view('dashboard.options.edit',$data);
    }


    public function update($id, OptionsRequest $request){

        try{

            $option= Option::find($id);

            if(!$option)
                return redirect()->route('admin.options')->with(['error'=>' لم يتم التحديث هناك خطأ بالبينات !!!']);

            $option->update($request->only(['price','product_id','attribute_id']));

            //save the translation attribute

            $option->name= $request->name;
            $option->save();

            return redirect()->route('admin.options')->with(['success'=>'تم تحديث البيانات']);

        }catch (\Exception $ex){
            return redirect()->route('admin.options')->with(['error'=>'لم يتم التحديث هناك خطأ بالبينات !!!']);
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
