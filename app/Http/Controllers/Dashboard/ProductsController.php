<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\GenralProductRequest;
use App\Http\Requests\MainCategoryRequest;
use App\Http\Requests\ProductPriceValidation;
use App\Http\Requests\ProductImagesRequest;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Image;
use App\Models\Product;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductsController extends Controller
{
    public function index(){

        $products = Product::select('id','slug','price', 'created_at')->paginate(PAGINATION_COUNT);
        return view('dashboard.products.general.index', compact('products'));
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

            $product= Product::create([
                'slug'=>$request->slug,
                'brand_id'=>$request->brand_id,
                'is_active'=>$request->is_active,
            ]);

            //save the translation attribute

            $product->name= $request->name;
            $product->description= $request->description;
            $product->short_description= $request->short_description;
            $product->save();

            // save product categories

            $product->categories()->attach($request->categories);

            DB::commit();
            return redirect()->route('admin.products')->with(['success'=>'تمت الاضافة بنجاح']);


        }catch (\Exception $ex){
            DB::rollBack();
            return redirect()->route('admin.products')->with(['error'=>' هناك خطأ بالبينات !!!']);
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


    public function update($id, MainCategoryRequest $request){

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
