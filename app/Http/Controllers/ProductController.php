<?php 

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

/** 
 * @OA\Schema(
 *   schema = "ProductSchema",
 *   title = "Product Model",
 *   description = "Product Model",
 *   @OA\Property(
 *     property="name",
 *     type="string",
 *     example="samasung A20",
 *     description="product name"
 *   ),
 *   @OA\Property(
 *     property="size",
 *     type="string",
 *     example="20x40mm",
 *     description="product size"
 *   ),
 *   @OA\Property(
 *     property="weight",
 *     type="decimal",
 *     example="54g",
 *     description="product weight"
 *   ),
 *   @OA\Property(
 *     property="cost",
 *     type="decimal",
 *     example="220",
 *     description="product cost"
 *   ),
 *   @OA\Property(
 *     property="quantity",
 *     type="integer",
 *     example="25",
 *     description="product quantity"
 *   ),
 *   @OA\Property(
 *     property="type",
 *     type="string",
 *     example="smartphone",
 *     description="product type"
 *   ),
 *   @OA\Property(
 *     property="expiredat",
 *     type="date",
 *     example="2021/04/11",
 *     description="product expiration date"
 *   ),
 *   @OA\Property(
 *     property="depot_id",
 *     type="integer",
 *     example="2",
 *     description="product depot"
 *   ),
 * )
 */



class ProductController extends Controller 
{
   
   
   /**
    * @OA\Get(
    *   tags={"Products"},
    *   path="/products/all",
    *   summary="Return all products",
    *   @OA\Response(
    *        response=200, 
    *       description="OK",
    *       @OA\JsonContent(
    *         type="object",
    *         @OA\Property(
    *                property="data", 
    *               type="array",
    *               @OA\Items(ref="#/components/schemas/ProductSchema")
    *    ),
    *       )
    *    ),
    *   @OA\Response(response=404, description="Not Found")
    * )
    */
    public function index() : JsonResponse {
        // Get products from database
        $products = DB::select('select * from products order by id desc');
       
        // Check if query return data
        if($products != null) 
            return response()->json(["data" => $products],200);
        else 
            return response()->json("There is not products in the database",404);
    }



    /**
     * @OA\Post(
     *   tags={"Products"},
     *   path="/products/create",
     *   summary="Product store",
     *   @OA\RequestBody(
     *     required=true,
     *     @OA\JsonContent(
     *       type="object",
     *       @OA\Property(property="name", type="string"),
     *       @OA\Property(property="size", type="string"),
     *       @OA\Property(property="weight", type="decimal"),
     *       @OA\Property(property="cost", type="decimal"),
     *       @OA\Property(property="quantity", type="decimal"),
     *       @OA\Property(property="type", type="string"),
     *       @OA\Property(property="expiredat", type="date"),
     *       @OA\Property(property="depot_id", type="integer"),
     *     )
     *   ),
     *   @OA\Response(
     *     response=201,
     *     description="OK",
     *     @OA\JsonContent(ref="#/components/schemas/ProductSchema")
     *   ),
     *   @OA\Response(response=422, description="Unprocessable Entity"),
     *   @OA\Response(response=500, description="Internal server error")
     * )
     */
    public function store(Request $request) : JsonResponse {
        // Validate given data
        $validated = $this->ValidateStoreRequest($request);
        $req_data = $request->all();
        $data = $this->ExtractData($req_data);
        if ($validated) {
            // Insert product into database
            if(!empty($data['expiredat']))
                $insert =  DB::insert('insert into products (name,size,weight,cost,quantity,type,expiredat,depot_id) values (?, ?, ?, ?, ?, ?, ?, ?)', $data);
            else 
                $insert =  DB::insert('insert into products (name,size,weight,cost,quantity,type,depot_id) values (?, ?, ?, ?, ?, ?, ?)', $data);
            // Check if insert query done with success
            if($insert)
                return response()->json("Data inserted with success",201);
            else 
                return response()->json("Data has not been inserted",500);
        }
        else 
            return response()->json("Given data not validated!",422);
    }




    /**
     * @OA\Get(
     *   tags={"Products"},
     *   path="/products/product/{id}",
     *   summary="Product show",
     *   @OA\Parameter(ref="#/components/parameters/id"),
     *   @OA\Response(
     *     response=200,
     *     description="OK",
     *     @OA\JsonContent(ref="#/components/schemas/ProductSchema")
     *   ),
     *   @OA\Response(response=404, description="Not Found")
     * )
     */
    public function show(int $id) : JsonResponse {
        // Get product from database
        $product = DB::select('select * from products where id = ?', [$id]);

        // Check if query return data 
        if($product != null)
            return response()->json(["data" => $product],200);
        else 
            return response()->json("We can't find wanted product in the database",404);
    }



    /**
     * @OA\Put(
     *   tags={"Products"},
     *   path="/products/update/{id}",
     *   summary="Product update",
     *   @OA\Parameter(ref="#/components/parameters/id"),
     *   @OA\RequestBody(
     *     required=true,
     *     @OA\JsonContent(
     *       type="object",
     *       @OA\Property(property="name", type="string"),
     *       @OA\Property(property="size", type="string"),
     *       @OA\Property(property="weight", type="decimal"),
     *       @OA\Property(property="cost", type="decimal"),
     *       @OA\Property(property="quantity", type="decimal"),
     *       @OA\Property(property="type", type="string"),
     *       @OA\Property(property="expiredat", type="date"),
     *       @OA\Property(property="depot_id", type="integer"),
     *     )
     *   ),
     *   @OA\Response(
     *     response=201,
     *     description="OK",
     *     @OA\JsonContent(ref="#/components/schemas/ProductSchema")
     *   ),
     *   @OA\Response(response=404, description="Not Found"),
     *   @OA\Response(response=422, description="Unprocessable Entity"),
     *   @OA\Response(response=500, description="Internal server error")
     * )
     */
    public function update(Request $request, int $id) : JsonResponse {
        // Check if product exists in the database
        $product = DB::select('select * from products where id = ?', [$id]);

        // Check if query return data
        if($product != null) {
            // Validate given request
            $validated = $this->ValidateUpdateRequest($request, $id);
            if($validated) {
            // Extract data
            $req_data = $request->all();
            $data = $this->ExtractDataWithId($req_data, $id);
            // Update data
            if(!empty($data['expiredat']))
                 $update = DB::update('update products set name = ?, size = ?, weight = ?, cost = ?, quantity = ?, type= ?, expiredat = ?, depot_id = ? where id = ?', $data);
             else 
                $update = DB::update('update products set name = ?, size = ?, weight = ?, cost = ?, quantity = ?, type= ?, depot_id = ? where id = ?', $data);

                // Check if update query done with success
                if($update)
                    return response()->json("Product has been updated",201);
                else 
                    return response()->json("Sorry product has not been updated",500);

            } 
            else 
                return response()->json("Given data has not been validated",422);
        }
        else  return response()->json("This provider does not exist in the database",404);
    }



    /**
     * @OA\Delete(
     *   tags={"Products"},
     *   path="/products/delete/{id}",
     *   summary="Summary",
     *   @OA\Parameter(ref="#/components/parameters/id"),
     *   @OA\Response(response=200, description="OK"),
     *   @OA\Response(response=500, description="Internal server error"),
     * )
     */
    public function delete(int $id) : JsonResponse {
        // Check if product exists in the database
        $product = DB::select('select * from products where id = ?', [$id]);
  
        // Check if query return data
        if($product != null) {
            // Delete product
            $delete = DB::delete('delete from products where id = ?', [$id]);
            // Check if delete query  done with success
            if($delete)
                return response()->json("Product has been deleted",200);
            else 
                return response()->json("Sorry product has not been deleted",500);
        }
        else 
            return response()->json("This product do not exist in the database");
    }




    /**
     * Validate given request data (store)
     * @var boolean
     */
    private function ValidateStoreRequest($request)  {
        $validated = $this->validate($request, [
            "name" => 'required|string|min:2|unique:products',
            "size" => 'required|string|min:3',
            "weight" => 'required|integer|min:1',
            "cost" => 'required|integer|min:1',
            "quantity" => 'required|integer|min:1',
            "type" => 'required|string',
            "depot_id" =>'required|integer|min:1'
        ]);

        if($validated) 
            return true;
        else 
            return false;
    }

    /**
     * Validate given request data (update)
     * @var boolean
     */
    private function ValidateUpdateRequest($request, $id) {
        $validated = $this->validate($request, [
            "name" => 'required|string|min:2|unique:products,name,'.$id,
            "size" => 'required|string|min:3',
            "weight" => 'required|integer|min:1',
            "cost" => 'required|integer|min:1',
            "quantity" => 'required|integer|min:1',
            "type" => 'required|string',
            "depot_id" =>'required|integer|min:1'
        ]);

        if($validated) 
            return true;
        else 
            return false;
    }


    /**
     * Extract data from given request (store)
     * @var array 
     */
    private function ExtractData(array $data) : array {
        if(!empty($data['expiredat']))
            return $data = [
                $data["name"],
                $data["size"],
                $data["weight"],
                $data["cost"],
                $data["quantity"],
                $data["type"],
                $data["expiredat"],
                $data["depot_id"],
            ];
        else 
            return $data = [
                $data["name"],
                $data["size"],
                $data["weight"],
                $data["cost"],
                $data["quantity"],
                $data["type"],
                $data["depot_id"],
            ];
    }


     /**
     * Extract data from given request (update)
     * @var array 
     */
    private function ExtractDataWithId(array $data, int $id) : array {
        if(!empty($data['expiredat']))
        return $data = [
            $data["name"],
            $data["size"],
            $data["weight"],
            $data["cost"],
            $data["quantity"],
            $data["type"],
            $data["expiredat"],
            $data["depot_id"],
            $id,
        ];
    else 
        return $data = [
            $data["name"],
            $data["size"],
            $data["weight"],
            $data["cost"],
            $data["quantity"],
            $data["type"],
            $data["depot_id"],
            $id,
        ];
    }



}