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
    public function index(){

        $options = Option::with(['product'=>function($prod){
            $prod->select('id');
        },'attribute'=>function($attr){
            $attr->select('id');
        }])->select('id','product_id','attribute_id', 'price')->paginate(PAGINATION_COUNT);
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

    public function getPrice($product_id){
        return view('dashboard.products.prices.create')->with('id',$product_id);
    }


    public function getStock($product_id){
        return view('dashboard.products.stock.create')->with('id',$product_id);
    }


    public function saveProductPrice(ProductPriceValidation $request){
        try {
            Product::whereId($request->product_id)->update($request->only(['price','special_price','special_price_typ','special_price_start','special_price_end']));
            return redirect()->route('admin.products')->with(['success'=>'تم التحديث بنجاح']);
        }catch (\Exception $X){
            return redirect()->route('admin.products')->with(['error'=>'لم يتم التحديث']);

        }
    }

    public function saveProductStock(ProductImagesRequest $request){
        try {
            Product::whereId($request->product_id)->update($request->except(['_token','product_id']));
            return redirect()->route('admin.products')->with(['success'=>'تم التحديث بنجاح']);
        }catch (\Exception $X){
            return redirect()->route('admin.products')->with(['error'=>'لم يتم التحديث']);

        }
    }


    public function addImages($product_id){
        return view('dashboard.products.images.create')->withId($product_id);
    }

    //to save product images in folder only

    public function saveProductImages(Request $request){
        $file=$request->file('dzfile');
        $filename=uploadImage('products',$file);

        return response()->json([
            'name'=>$filename,
            'original_name'=>$file->getClientOriginalName(),
        ]);

    }

    //to save product images in DB

    public function saveProductImagesDB(ProductImagesRequest $request){
        try{
            if($request->has('document') && count($request->document)>0){
                foreach ($request->document as $image){
                    Image::create([
                       'product_id'=>$request->product_id,
                       'photo'=>$image,
                    ]);
                }
            }
            return redirect()->route('admin.products')->with(['success'=>'تم التحديث بنجاح']);
        }catch (\Exception $ex){
            return redirect()->route('admin.products')->with(['error'=>'لم يتم التحديث']);
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
