<?php

namespace App\Http\Controllers;
use Session;
use Hash;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Mail\ContactEmail;
use App\Models\CustomerUser;
use App\Models\Product;
use App\Models\Category;
use App\Models\Seller;
use App\Models\Comment;
use App\Models\Admin;
use Mail;

// ly do luc dau ham delete sai vi khong the truyen qua seller id , ham update co id cua seller product da co seller id 
// san roi , ham read duoc truyen truc tiep seller id qua dia chi , con delete chi truyen duoc id cua product thoi 
// -> lam bang form hay dia chi nhu nhau , khac cho delete xoa bang form -> co the truyen it san pham va idseller
// -> return view su dung dia chi cua phuong thuc hien co , co the la post hoac get -> no khong quan trong
// man hinh hien thi view cua 1 blade.php ma minh chon va minh se gan thuoc tinh cho no , khi nhan vo 1 chuc nang 
// vi du nhu sap xep no se truyen qua controller ma con troller do tra ve 1 view khac... ma controller do 
// co 1 route khac nua nen thanh dia chi se thay doi theo luon 
// -> return direct tra ve 1 route trong web, route do co controller thuc hien 1 view gi do...
// va no se tra ra man hinh nhu vay , tuy nhien vi du bai cua thay tra ve list vi controller list 
// da truyen day du thuoc tinh nen man hinh se hien thi ket qua minh mong muon 
// con do an truyen qua form , phai nhan form moi co id ma khi tra ve redirect thi lai khong nhan vao 
// form nen khong co id nen tra ve ket qua khong mong muon



class CrudCustomerUsersController extends Controller
{
    public function viewRegister(Request $request)
    {
        return view('auth.register');
    }
    public function formRegister(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required',
            'password' => 'required',
        ]);
    
        $role = $request->get('role');
        $data = $request->all();
        $hashedPassword = hash::make($data['password']);
        if($role == 'seller') {
            if (CustomerUser::where('email', $data['email'])->exists()) {
                return redirect('Register');
            }
           else {
            $check = Seller::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => $hashedPassword
            ]);
            return redirect('Login');
           }
        }
        else if ($role == 'customer') {
            if (Seller::where('email', $data['email'])->exists()) {
                return redirect('Register');
            }
            else {
                $check = CustomerUser::create([
                    'name' => $data['name'],
                    'email' => $data['email'],
                    'password' => $hashedPassword,
                ]);
                return redirect('Login');
            }
        }
    }
    public function viewLogin(Request $request)
    {
      
        return view('auth.login');
    }
    public function login(Request $request) {
       
        $request->validate([
            'email' => 'required',
            'password' => 'required',
        ]);
    
        $role = $request->get('role');
        $email = $request->get('email');

        //neu co loi thi ham bat loi ben kia se hien len
        // lưu vào đây để xác thực hàm auth
        
        $credentials = $request->only('email', 'password');
        if($role == 'seller') {
            if (Auth::guard('tbl_sellers')->attempt($credentials)) { 
                  session(['emailSeller' => $email]); 
                $Seller = Seller::where('email', $email)->first();
                $products = Product::with('Category')->where('seller_id', $Seller->id)->orderByDesc('id')->get();
                $sellerTotal = Product::with('Category')->where('seller_id',$Seller->id)->count(); 
                return view('auth.seller', ['products' => $products,'idSeller' => $Seller->id, 'sellerTotal' => $sellerTotal]);
            }
        }
        else if($role == 'customer') {
            if (Auth::guard('tbl_customer_users')->attempt($credentials)) { 
                session(['emailCustomerUser' => $email]); 
                $customerUser = CustomerUser::where('email', $email)->first();
                $products = Product::with('Category')->orderByDesc('id')->paginate(10);
                $productTotal = Product::count();
                $pages = ceil($productTotal)/3;
                $categories = Category::get();
                return view('auth.home', ['idCustomer' => $customerUser , 'products' => $products,'categories' => $categories, 'pages' => $pages]);
            }
        }
        else if($role == 'admin') {
            if (Auth::guard('table_admin')->attempt($credentials)) { 
                session(['emailAdmin' => $email]); 
                return redirect('adminUserCustomer');
            }
        }
        
        return view('auth.login');
        
    }
   
    public function viewResetPassword1(Request $request) {
        return view('auth.resetPassword1');
    }    
    public function viewResetPassword2(Request $request) {
        return view('auth.resetPassword2');
    }   
    public function viewResetPassword3(Request $request) {
        return view('auth.resetPassword3');
    }    
    public function formResetPassword1(Request $request) {
        $email = $request->get('email');
        $role = $request->get('role');
        $credentials = $request->only('email', 'password');
        if ($role == "customer") {
            if (Auth::guard('tbl_customer_users')->attempt($credentials)) {
                session(['emailResetPasswordCustomerUser' => $email]);
                $emailCustomerUser = session('emailResetPasswordCustomerUser');
                $customerUser = CustomerUser::where('email', $emailCustomerUser)->first();
                $nameCustomerUser = $customerUser->name;
                session(['nameCustomerUser' => $nameCustomerUser]);
                $ranDomResetPass = rand(1000, 9999);
                session(['codeResetPassword' => $ranDomResetPass]);
                Mail::to($emailCustomerUser)->send(new ContactEmail());
                return redirect('resetPassword2');
            }
        }
        else if($role == "seller") {
            if (Auth::guard('tbl_sellers')->attempt($credentials)) {
                session(['emailResetPasswordSeller' => $email]);
                $emailSeller = session('emailResetPasswordSeller');
                $seller = Seller::where('email', $emailSeller)->first();
                $nameSeller = $seller->name;
                session(['nameSeller' => $nameSeller]);
                $ranDomResetPass = rand(1000, 9999);
                session(['codeResetPassword' => $ranDomResetPass]);
                Mail::to($emailSeller)->send(new ContactEmail());
                return redirect('resetPassword2');
            }
        }
        else if($role == "admin") {
            if (Auth::guard('table_admin')->attempt($credentials)) {
                session(['emailResetPasswordAdmin' => $email]);   
                $emailAdmin = session('emailResetPasswordAdmin');
                $admin = Admin::where('email', $emailAdmin)->first();
                $nameAdmin = $admin->name;
                session(['nameAdmin' => $nameAdmin]);
                $ranDomResetPass = rand(1000, 9999);
                session(['codeResetPassword' => $ranDomResetPass]);
                Mail::to($emailAdmin)->send(new ContactEmail());
                return redirect('resetPassword2');
            }
        }
        return redirect('resetPassword1');
    }
    
    public function formResetPassword2(Request $request) {
        $code = $request->get('code');
        $codeResetPassword = session("codeResetPassword");
        if($code == $codeResetPassword) {
            return redirect('resetPassword3');     
        }
        return redirect('resetPassword2');
    }  
    public function formResetPassword3(Request $request) {
        if (session()->has('emailResetPasswordCustomerUser')) {
            $password = $request->get('Password');
            $emailCustomerUser = session('emailResetPasswordCustomerUser');
            $customerUser = CustomerUser::where('email', $emailCustomerUser)->first();
            $customerUser->password = Hash::make($password); 
            $customerUser->save();
            Session::flush();
            return redirect('Login');  
        } else if (session()->has('emailResetPasswordSeller')) { // Added session check
            $password = $request->get('Password');
            $emailSeller = session('emailResetPasswordSeller');
            $seller = Seller::where('email', $emailSeller)->first(); // Fixed variable name
            $seller->password = Hash::make($password);  // Fixed variable name
            $seller->save();
            Session::flush();
            return redirect('Login');  // Added redirection after saving password
        }
    else if (session()->has('emailResetPasswordAdmin')) { // Added session check
        $password = $request->get('Password');
        $emailAdmin = session('emailResetPasswordAdmin');
        $Admin = Admin::where('email', $emailAdmin)->first(); // Fixed variable name
        $Admin->password = Hash::make($password);  // Fixed variable name
        $Admin->save();
        Session::flush();
        return redirect('Login');  // Added redirection after saving password
    }
        return redirect('resetPassword3');
    }
    public function viewUserProfile(Request $request) {
        $customerUserId = $request->get('id');
        $customerUser = CustomerUser::find($customerUserId);
        $dob = $customerUser->DOB;
        if($dob == null) {
            $dob = "1990-01-01";
        }
        $parts = explode('-', $dob);
        $year = $parts[0];
        $month = $parts[1];
        $day = $parts[2];
        return view('auth.account.profile', [
            'customerUser' => $customerUser,
            'year' => $year,
            'month' => $month,
            'day' => $day
        ]);
    }
    public function viewSellerProfile(Request $request) {
        $sellerId = $request->get('id');
        $seller = Seller::find($sellerId);
        $dob = $seller->DOB;
        if($dob == null) {
            $dob = "1990-01-01";
        }
        $parts = explode('-', $dob);
        $year = $parts[0];
        $month = $parts[1];
        $day = $parts[2];
        return view('auth.account.sellerProfile', [
            'seller' => $seller,
            'year' => $year,
            'month' => $month,
            'day' => $day
        ]);
    }
    
    public function updateUserProfile(Request $request)  {   
        $input = $request->all();
        $dob = $request->get('year') . "-" . $request->get('month') . "-" . $request->get('day');
        $customerUserId = $request->get('id');
        $customerUser = CustomerUser::find($customerUserId);
        $parts = explode('-', $dob);
        $year = $parts[0];
        $month = $parts[1];
        $day = $parts[2];
        $customerUser->name = $input['name'];
        $customerUser->username = $input['username'];
        $customerUser->email = $input['email'];
        $customerUser->phone = $input['phone'];
        $customerUser->address = $input['address'];
        $customerUser->sex =  $input['sex'];
        $customerUser->DOB =  $dob;
        $products = Product::with('Category')->get();

        if ($request->hasFile('img')) {
            // Xóa hình ảnh cũ (nếu có)
            Storage::delete('img/img_auth/' . $customerUser->img);
    
            // Lưu hình ảnh mới vào thư mục lưu trữ
            $image = $request->file('img');
            $imageName = time().'.'.$image->getClientOriginalExtension();
            $image->move(public_path('img/img_auth'), $imageName);
    
            // Cập nhật tên hình ảnh mới cho sản phẩm
            $customerUser->img = $imageName;   
        }
        $customerUser->save();
        return view('auth.account.profile', [
            'customerUser' => $customerUser,
            'year' => $year,
            'month' => $month,
            'day' => $day
        ]);
      
    }
    public function updateSellerProfile(Request $request)  {   
        $input = $request->all();
        $dob = $request->get('year') . "-" . $request->get('month') . "-" . $request->get('day');
        $sellerId = $request->get('id');
        $seller = Seller::find($sellerId);
        $parts = explode('-', $dob);
        $year = $parts[0];
        $month = $parts[1];
        $day = $parts[2];
        session(['emailSeller' => $seller->email]); 
        $seller->name = $input['name'];
        $seller->username = $input['username'];
        $seller->email = $input['email'];
        $seller->phone = $input['phone'];
        $seller->address = $input['address'];
        $seller->sex =  $input['sex'];
        $seller->DOB =  $dob;
        $products = Product::with('Category')->get();
      

        if ($request->hasFile('img')) {
            // Xóa hình ảnh cũ (nếu có)
            Storage::delete('img/img_auth/' . $seller->img);
    
            // Lưu hình ảnh mới vào thư mục lưu trữ
            $image = $request->file('img');
            $imageName = time().'.'.$image->getClientOriginalExtension();
            $image->move(public_path('img/img_auth'), $imageName);
    
            // Cập nhật tên hình ảnh mới cho sản phẩm
            $seller->img = $imageName;   
        }
        $seller->save();
        return view('auth.account.sellerProfile', [
            'seller' => $seller,
            'year' => $year,
            'month' => $month,
            'day' => $day
        ]);
      
    }
    public function viewSeller(Request $request)
    {   
      
        $idSeller = $request->get('id_seller');
      

        if ($request->has('oldest')) {
            $products = Product::with('Category')->where('seller_id', $idSeller)->orderBy('id')->get();
            $sellerTotal = Product::with('Category')->where('seller_id',$idSeller)->count();  
            return view('auth.seller', ['products' => $products,'idSeller' => $idSeller, 'sellerTotal' => $sellerTotal]);
        } else if ($request->has('newest')) {
            $products = Product::with('Category')->where('seller_id', $idSeller)->orderByDesc('id')->get();
            $sellerTotal = Product::with('Category')->where('seller_id',$idSeller)->count();  
            return view('auth.seller', ['products' => $products,'idSeller' => $idSeller, 'sellerTotal' => $sellerTotal]);
        }
    
    
        else if($request->has('bestselling')) {
            $products = Product::with('Category')->where('seller_id', $idSeller)->orderBy('sold')->get();  
            $sellerTotal = Product::with('Category')->where('seller_id',$idSeller)->count();  
            return view('auth.seller', ['products' => $products,'idSeller' => $idSeller, 'sellerTotal' => $sellerTotal]);
        }
        else if($request->has('priceDESC')) {
            $products = Product::with('Category')->where('seller_id', $idSeller)->orderBy('price')->get();
            $sellerTotal = Product::with('Category')->where('seller_id',$idSeller)->count();  
            return view('auth.seller', ['products' => $products,'idSeller' => $idSeller, 'sellerTotal' => $sellerTotal]);
        }
        else if($request->has('priceASC')) {
            $products = Product::with('Category')->where('seller_id', $idSeller)->orderByDESC('price')->get();
            $sellerTotal = Product::with('Category')->where('seller_id',$idSeller)->count();  
            return view('auth.seller', ['products' => $products,'idSeller' => $idSeller, 'sellerTotal' => $sellerTotal]);
        }
        else {
            $products = Product::with('Category')->where('seller_id',$idSeller)->get(); 
            $sellerTotal = Product::with('Category')->where('seller_id',$idSeller)->count();  
            return view('auth.seller', ['products' => $products,'idSeller' => $idSeller, 'sellerTotal' => $sellerTotal]);
            // return redirect('seller');
        }
    }
    public function viewAddProduct(Request $request)
    {   
            $categories = Category::all();
            return view('auth.product',['categories' => $categories, 'seller_id' => $request->get('id')]);
        // return view('auth.product'); 
    }

    public function addProduct(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'price' => 'required',
            'quantity' => 'required',
            'des' => 'required',
            'category_id' => 'required',
            'seller_id' => 'required',
            'img' => 'image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $data = $request->all();
  
        if ($request->hasFile('img')) {
            // Lưu hình ảnh vào thư mục lưu trữ

            //luu hinh anh vao $image
            $image = $request->file('img');

            //dat ten cho hinh anh neu chon 2 hinh giong nhau
            $imageName = time().'.'.$image->getClientOriginalExtension();

            //luu hinh anh vao 
            $image->move(public_path('img/img_auth'), $imageName);
    
            // Thêm tên hình ảnh vào dữ liệu để lưu vào cơ sở dữ liệu
            $data['img'] = $imageName;
        }
    
   

        $check = Product::create([
            'product_name' => $data['name'],
            'description' => $data['des'],
            'price' => $data['price'],
            'quantity' => $data['quantity'],
            'img' => $data['img'],
            'category_id' => $data['category_id'],
            'seller_id' => $data['seller_id']
            
        ]);
        $products = Product::with('Category')->where('seller_id', $request->get('seller_id'))->orderByDESC('id')->get();
        
        $sellerTotal = Product::with('Category')->where('seller_id',$request->get('seller_id'))->count();  
        return view('auth.seller', ['products' => $products,'idSeller' =>  $request->get('seller_id'), 'sellerTotal' => $sellerTotal]);

      
 
    }
    public function viewDetailProduct(Request $request)
    {   
            $productId = $request->get('id');
            $product = Product::with('Category')->find($productId);  
            return view('auth.product_detail',['product' => $product]);
    }
    public function viewDetailProductIndexCusTomerUser(Request $request)
    {   
            $productId = $request->get('productId');
            $customerUserId = $request->get('customerUserId');
            $product = Product::with('Category')->find($productId);  
            $seller = Seller::find($product->seller_id);
            $userComments = Comment::with('CustomerUser')->where('productId',$productId)->orderBy('created_at', 'desc')->get();

            $customerUser = CustomerUser::find($customerUserId);
            $totalComments = Comment::where('productId',$productId)->count();
            $oneStar = Comment::where('productId',$productId)->where('star',1)->count();
            $twoStar = Comment::where('productId',$productId)->where('star',2)->count();
            $threeStar = Comment::where('productId',$productId)->where('star',3)->count();
            $fourStar = Comment::where('productId',$productId)->where('star',4)->count();
            $fiveStar = Comment::where('productId',$productId)->where('star',5)->count();

          
            $totalStar = Comment::where('productId',$productId)->sum('star');
            $evarageStars = ($totalComments > 0) ? round( $totalStar / $totalComments,1)  : 0;
            
            if(is_float($evarageStars) == $evarageStars) {
                $evarageStars = (int)$evarageStars;
            }

            $percenOneStar = ($totalComments > 0) ? ceil(($oneStar / $totalComments) * 100) : 0;
            $percenTwoStar = ($totalComments > 0) ? ceil(($twoStar / $totalComments) * 100) : 0;
            $percenThreeStar = ($totalComments > 0) ? ceil(($threeStar / $totalComments) * 100) : 0;
            $percenFourStar = ($totalComments > 0) ? ceil(($fourStar / $totalComments) * 100) : 0;
            $percenFiveStar = ($totalComments > 0) ? ceil(($fiveStar / $totalComments) * 100) : 0;
        
            return view('auth.product_detail_customerUser', ['product' => $product, 'seller' => $seller, 'customerUser' => $customerUser, 'userComments' => $userComments, 'totalComments' => $totalComments,'percenOneStar' => $percenOneStar,'percenTwoStar' => $percenTwoStar,'percenThreeStar' => $percenThreeStar,'percenFourStar' => $percenFourStar, 'percenFiveStar' => $percenFiveStar, 'evarageStars' => $evarageStars]);
    }
    public function formComment(Request $request)
    {     
        $request->validate([
            'description' => 'required',
            'img' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
            'productId' => 'required',
            'customerUserId' => 'required',
            'star' => 'required',
        ]);
        if ($request->isMethod('post')) {

        }
        $data = $request->all();
        if ($request->hasFile('img')) {
            // Lưu hình ảnh vào thư mục lưu trữ

            //luu hinh anh vao $image
            $image = $request->file('img');

            //dat ten cho hinh anh neu chon 2 hinh giong nhau
            $imageName = time().'.'.$image->getClientOriginalExtension();

            //luu hinh anh vao 
            $image->move(public_path('img/img_auth'), $imageName);
    
            // Thêm tên hình ảnh vào dữ liệu để lưu vào cơ sở dữ liệu
            $data['img'] = $imageName;
        }
        $check = Comment::create([
            'description' => $data['description'],
            'img' => $data['img'],
            'productId' => $data['productId'],
            'customerUserId' => $data['customerUserId'],
            'star' => $data['star'],
        ]);

            $productId = $request->get('productId');
            $customerUserId = $request->get('customerUserId');  
            $product = Product::with('Category')->find($productId);  
            $seller = Seller::find($product->seller_id);
            $userComments = Comment::with('CustomerUser')->where('productId',$productId)->orderBy('created_at', 'desc')->get();
            $customerUser = CustomerUser::find($customerUserId);
            $totalComments = Comment::where('productId',$productId)->count();
            $oneStar = Comment::where('productId',$productId)->where('star',1)->count();
            $twoStar = Comment::where('productId',$productId)->where('star',2)->count();
            $threeStar = Comment::where('productId',$productId)->where('star',3)->count();
            $fourStar = Comment::where('productId',$productId)->where('star',4)->count();
            $fiveStar = Comment::where('productId',$productId)->where('star',5)->count();
            $totalStar = Comment::where('productId',$productId)->sum('star');

            $evarageStars = $totalStar / $totalComments;
            
        
            $floorEvarageStars = is_float($evarageStars) ? floor($evarageStars) : 0;

            $percenOneStar = ($totalComments > 0) ? ceil(($oneStar / $totalComments) * 100) : 0;
            $percenTwoStar = ($totalComments > 0) ? ceil(($twoStar / $totalComments) * 100) : 0;
            $percenThreeStar = ($totalComments > 0) ? ceil(($threeStar / $totalComments) * 100) : 0;
            $percenFourStar = ($totalComments > 0) ? ceil(($fourStar / $totalComments) * 100) : 0;
            $percenFiveStar = ($totalComments > 0) ? ceil(($fiveStar / $totalComments) * 100) : 0;
        
            return view('auth.product_detail_customerUser', [
                'product' => $product, 
                'seller' => $seller, 
                'customerUser' => $customerUser, 
                'userComments' => $userComments, 
                'totalComments' => $totalComments,
                'percenOneStar' => $percenOneStar,
                'percenTwoStar' => $percenTwoStar,
                'percenThreeStar' => $percenThreeStar,
                'percenFourStar' => $percenFourStar, 
                'percenFiveStar' => $percenFiveStar, 
                'evarageStars' =>  $evarageStars,
            ]);
        }
    public function arrangeIndexUserCustomer(Request $request)
    {   
        $customerUserId = $request->get('customerUserId');
        $customerUser = CustomerUser::find($customerUserId);
         if($request->has('newest')) {
            session(['sort_type' => 'newest']);
            $products = Product::with('Category')->orderByDESC('id')->paginate(10);
            $categories = Category::get();
            $productTotal = Product::count();
            $pages = ceil($productTotal)/3;
            return view('auth.home', ['idCustomer' => $customerUser , 'products' => $products,'categories' => $categories, 'pages' => $pages]);
           }
        else if($request->has('oldest')) {
            session(['sort_type' => 'oldest']);
            $products = Product::with('Category')->orderBy('id')->paginate(10);
            $categories = Category::get();
            $productTotal = Product::count();
            $pages = ceil($productTotal)/3;
            return view('auth.home', ['idCustomer' => $customerUser , 'products' => $products,'categories' => $categories, 'pages' => $pages]);
           }
        else if($request->has('bestselling')) {
            session(['sort_type' => 'bestselling']);
            $products = Product::with('Category')->orderBy('sold')->paginate(10);
            $categories = Category::get();
            $productTotal = Product::count();
            $pages = ceil($productTotal)/3;
            return view('auth.home', ['idCustomer' => $customerUser , 'products' => $products,'categories' => $categories, 'pages' => $pages]);
           }
         else if($request->has('priceASC')) {
            session(['sort_type' => 'priceASC']);
            $products = Product::with('Category')->orderByDESC('price')->paginate(10);
            $categories = Category::get();
            $productTotal = Product::count();
            $pages = ceil($productTotal)/3;
            return view('auth.home', ['idCustomer' => $customerUser , 'products' => $products,'categories' => $categories, 'pages' => $pages]);
           }
         else if($request->has('priceDESC')) {
            session(['sort_type' => 'priceDESC']);
            $products = Product::with('Category')->orderBy('price')->paginate(10);
            $categories = Category::get();
            $productTotal = Product::count();
            $pages = ceil($productTotal)/3;
            return view('auth.home', ['idCustomer' => $customerUser , 'products' => $products,'categories' => $categories, 'pages' => $pages]);
           }

    }
    public function viewProductPage(Request $request)
    {   
    if (session()->has('emailCustomerUser')) {
      
        if (session()->has('sort_type')) {
            $sortType = session('sort_type');
            if ($sortType === 'newest') {
                $products = Product::with('Category')->orderByDesc('id')->paginate(10);
            } elseif ($sortType === 'oldest') {
                $products = Product::with('Category')->orderBy('id')->paginate(10);
            } elseif ($sortType === 'bestselling') {
                $products = Product::with('Category')->orderBy('sold')->paginate(10);
            } elseif ($sortType === 'priceASC') {
                $products = Product::with('Category')->orderByDESC('price')->paginate(10);
            } elseif ($sortType === 'priceDESC') {
                $products = Product::with('Category')->orderBy('price')->paginate(10);
            }
            // Tạo các biến $categories, $productTotal, $pages chỉ khi cần thiết
            $categories = Category::get();
            $productTotal = Product::count();
            $pages = ceil($productTotal) / 3;

            // Lấy thông tin khách hàng nếu cần
            $email = session('emailCustomerUser');
            $customerUser = CustomerUser::where('email', $email)->first();  

            return view('auth.home', ['idCustomer' => $customerUser, 'products' => $products, 'categories' => $categories, 'pages' => $pages]);
        }
        else {
            $products = Product::with('Category')->orderByDesc('id')->paginate(10);
            $categories = Category::get();
            $productTotal = Product::count();
            $pages = ceil($productTotal) / 3;

            // Lấy thông tin khách hàng nếu cần
            $email = session('emailCustomerUser');
            $customerUser = CustomerUser::where('email', $email)->first();  

            return view('auth.home', ['idCustomer' => $customerUser, 'products' => $products, 'categories' => $categories, 'pages' => $pages]);
        }
    }
}

    public function returnHome(Request $request)
    {   
        if(session()->has('emailCustomerUser')) {
         $email = session('emailCustomerUser');
        $customerUser = CustomerUser::where('email', $email)->first();  
        $products = Product::with('Category')->orderByDESC('id')->get();
        $categories = Category::get();
        $productTotal = Product::count();
        $pages = ceil($productTotal)/3;
        return view('auth.home', ['idCustomer' => $customerUser, 'products' => $products, 'categories' => $categories, "pages" => $pages]);
    }    
        else if(session()->has('emailSeller')) {
            $email = session('emailSeller');
            $seller = Seller::where('email',$email)->first();
            $idSeller = $seller->id;
            $products = Product::with('Category')->where('seller_id', $idSeller)->orderByDESC('price')->get();
            $sellerTotal = Product::with('Category')->where('seller_id',$idSeller)->count();  
            return view('auth.seller', ['products' => $products,'idSeller' => $idSeller, 'sellerTotal' => $sellerTotal]);
            
        }
        else if(session()->has('emailAdmin')) {
            return redirect('adminUserCustomer');
        }
    }
    public function searchProduct(Request $request) {
        if(session()->has('emailCustomerUser')) {
            $email = session('emailCustomerUser');
            $customerUser = CustomerUser::where('email', $email)->first();  
            $keyword = $request->get('keyword');
            $products = Product::with('Category')->where('product_name', 'like', "%$keyword%")->get(); // Thêm dấu % vào xung quanh từ khóa
            $categories = Category::get();
            $productTotal = $products->count(); // Đếm số sản phẩm tìm được
            $pages = ceil($productTotal / 3);
            return view('auth.home', ['idCustomer' => $customerUser, 'products' => $products, 'categories' => $categories, "pages" => $pages]);
        }
    }
    public function viewProductCate(Request $request)
    {   
       if(session()->has('emailCustomerUser')) {
        $email = session('emailCustomerUser');
        $customerUser = CustomerUser::where('email', $email)->first();
        $products = Product::with('Category')->where('category_id',$request->id)->orderByDESC('id')->get();
        $categories = Category::get();
        $productTotal = Product::count();
        $pages = ceil($productTotal)/3;

       }
       return view('auth.home', ['idCustomer' => $customerUser, 'products' => $products, 'categories' => $categories, "pages" => $pages]);
}
    public function deleteProduct(Request $request)
    {      
            $productId = $request->get('productId');
            $idSeller = $request->get('id_seller');
            $product = Product::destroy($productId);
          
            $products = Product::with('Category')->where('seller_id',$idSeller)->get(); 
         
            $sellerTotal = Product::with('Seller')->where('seller_id',$idSeller)->count();  
            return view('auth.seller', ['products' => $products,'idSeller' => $idSeller, 'sellerTotal' => $sellerTotal]);
         
    }
    public function viewUpdateProduct(Request $request)
    {   
        $productId = $request->get('id');
        $product = Product::find($productId);
        $categories = Category::all();
        return view('auth.update',['product' => $product,'categories' => $categories]);
    }
    public function updateProduct(Request $request)
    {   
        $input = $request->all();
        $idSeller = $request->get('id');
        $product = Product::find($input['id']);
        $product -> product_name = $input['name'];
        $product -> price = $input['price'];
        $product -> description = $input['des'];
        $product -> quantity = $input['quantity'];
        $product -> category_id = $input['category_id'];
      
        if ($request->hasFile('img')) {
            // Xóa hình ảnh cũ (nếu có)
            Storage::delete('img/img_auth/' . $product->img);
    
            // Lưu hình ảnh mới vào thư mục lưu trữ
            $image = $request->file('img');
            $imageName = time().'.'.$image->getClientOriginalExtension();
            $image->move(public_path('img/img_auth'), $imageName);
    
            // Cập nhật tên hình ảnh mới cho sản phẩm
            $product->img = $imageName;
            
        }
        $product -> save();
        $products = Product::with('Category')->where('seller_id', $request->get('seller_id'))->orderBy('id')->get();
        $sellerTotal = Product::with('Category')->where('seller_id',$request->get('seller_id'))->count();
        return view('auth.seller', ['products' => $products, 'idSeller' => $request->get('seller_id'), 'sellerTotal' => $sellerTotal]);
    } 
    public function viewAdminUserCustomer() {
        $customerUsers = CustomerUser::orderByDESC('id')->get();
        return view('auth.adminUserCustomer',['customerUsers' => $customerUsers]);
    }
    public function viewAdminSeller() {
        $sellers =  Seller::orderByDESC('id')->get();
        return view('auth.adminSeller',['sellers' => $sellers]);
    }
    public function deleteAdminSeller(Request $request) {
       $idSeller = $request->get('id'); 
       $product = Seller::destroy($idSeller);
       return redirect('adminSeller');
    }
    public function deleteAdminCustomerUser(Request $request) {
        $idCustomerUser = $request->get('id'); 
        $product = CustomerUser::destroy($idCustomerUser);
        return redirect('adminUserCustomer');
     }
     public function viewAddAdminCustomerUser(Request $request) {
        return view('auth.addAdminCustomerUser');
     } 
     public function viewAddAdminSeller(Request $request) {
        return view('auth.addAdminSeller');
     } 
     public function viewUpdateAdminSeller(Request $request) {
        $sellerId = $request->get('id');
        $seller = Seller::find($sellerId);
        $dob = $seller->DOB;
        if($dob == null) {
            $dob = "1990-01-01";
        }
        $parts = explode('-', $dob);
        $year = $parts[0];
        $month = $parts[1];
        $day = $parts[2];
        $passwordUnhash = $seller->password;
        return view('auth.updateAdminSeller', [
            'seller' => $seller,
            'passwordUnhash' => $passwordUnhash,
            'year' => $year,
            'month' => $month,
            'day' => $day
        ]);
     } 
     public function viewUpdateAdminCustomerUser(Request $request) {
        $customerUserId = $request->get('id');
        $customerUser = CustomerUser::find($customerUserId);
        $dob = $customerUser->DOB;
        if($dob == null) {
            $dob = "1990-01-01";
        }
        $parts = explode('-', $dob);
        $year = $parts[0];
        $month = $parts[1];
        $day = $parts[2];
        $passwordUnhash = $customerUser->password;
        return view('auth.updateAdminCustomerUser', [
            'customerUser' => $customerUser,
            'passwordUnhash' => $passwordUnhash,
            'year' => $year,
            'month' => $month,
            'day' => $day
        ]);
     } 
     public function formUpdateAdminCustomerUser(Request $request) {
        $input = $request->all();
        $dob = $request->get('year') . "-" . $request->get('month') . "-" . $request->get('day');
        $CustomerUserId = $request->get('id');
        $CustomerUser = CustomerUser::find($CustomerUserId);
        $parts = explode('-', $dob);
        $year = $parts[0];
        $month = $parts[1];
        $day = $parts[2];
        $CustomerUser->name = $input['name'];
        $CustomerUser->username = $input['username'];
        $CustomerUser->email = $input['email'];
        $CustomerUser->phone = $input['phone'];
        $CustomerUser->address = $input['address'];
        $CustomerUser->sex =  $input['sex'];
        $CustomerUser->DOB =  $dob; 

        if ($request->hasFile('img')) {
            // Xóa hình ảnh cũ (nếu có)
            Storage::delete('img/img_auth/' . $CustomerUser->img);
    
            // Lưu hình ảnh mới vào thư mục lưu trữ
            $image = $request->file('img');
            $imageName = time().'.'.$image->getClientOriginalExtension();
            $image->move(public_path('img/img_auth'), $imageName);
    
            // Cập nhật tên hình ảnh mới cho sản phẩm
            $CustomerUser->img = $imageName;   
        }
        $CustomerUser->save();
        // return view('auth.updateAdminCustomerUser', [
        //     'CustomerUser' => $CustomerUser,
        //     'passwordUnhash' => $CustomerUser->password,
        //     'year' => $year,
        //     'month' => $month,
        //     'day' => $day
        // ]);
        return redirect('adminUserCustomer');
     }
     public function formUpdateAdminSeller(Request $request) {
        $input = $request->all();
        $dob = $request->get('year') . "-" . $request->get('month') . "-" . $request->get('day');
        $sellerId = $request->get('id');
        $seller = Seller::find($sellerId);
        $parts = explode('-', $dob);
        $year = $parts[0];
        $month = $parts[1];
        $day = $parts[2];
        $seller->name = $input['name'];
        $seller->username = $input['username'];
        $seller->email = $input['email'];
        $seller->phone = $input['phone'];
        $seller->address = $input['address'];
        $seller->sex =  $input['sex'];
        $seller->DOB =  $dob; 

        if ($request->hasFile('img')) {
            // Xóa hình ảnh cũ (nếu có)
            Storage::delete('img/img_auth/' . $seller->img);
    
            // Lưu hình ảnh mới vào thư mục lưu trữ
            $image = $request->file('img');
            $imageName = time().'.'.$image->getClientOriginalExtension();
            $image->move(public_path('img/img_auth'), $imageName);
    
            // Cập nhật tên hình ảnh mới cho sản phẩm
            $seller->img = $imageName;   
        }
        $seller->save();
        // return view('auth.updateAdminSeller', [
        //     'seller' => $seller,
        //     'passwordUnhash' => $seller->password,
        //     'year' => $year,
        //     'month' => $month,
        //     'day' => $day
        // ]);
        return redirect('adminSeller');
     }
     public function formAddAdminCustomerUser(Request $request) {
        $data = $request->all();
        $dob = $request->get('year') . "-" . $request->get('month') . "-" . $request->get('day');
        if ($request->hasFile('img')) {
            // Lưu hình ảnh vào thư mục lưu trữ

            //luu hinh anh vao $image
            $image = $request->file('img');

            //dat ten cho hinh anh neu chon 2 hinh giong nhau
            $imageName = time().'.'.$image->getClientOriginalExtension();

            //luu hinh anh vao 
            $image->move(public_path('img/img_auth'), $imageName);
    
            // Thêm tên hình ảnh vào dữ liệu để lưu vào cơ sở dữ liệu
            $data['img'] = $imageName;
        }
        $hashedPassword = hash::make($data['password']);
        $check = CustomerUser::create([
            'username' => $data['username'],
            'password' => $hashedPassword,
            'name' => $data['name'],
            'email' => $data['email'],
            'img' => $data['img'],
            'phone' => $data['phone'],
            'sex' => $data['sex'],
            'address' => $data['address'],
            'DOB' => $dob,
        ]);
        return redirect('adminUserCustomer');
     } 
     public function formAddAdminSeller(Request $request) {
        $data = $request->all();
        $dob = $request->get('year') . "-" . $request->get('month') . "-" . $request->get('day');
        if ($request->hasFile('img')) {
            // Lưu hình ảnh vào thư mục lưu trữ

            //luu hinh anh vao $image
            $image = $request->file('img');

            //dat ten cho hinh anh neu chon 2 hinh giong nhau
            $imageName = time().'.'.$image->getClientOriginalExtension();

            //luu hinh anh vao 
            $image->move(public_path('img/img_auth'), $imageName);
    
            // Thêm tên hình ảnh vào dữ liệu để lưu vào cơ sở dữ liệu
            $data['img'] = $imageName;
        }
        $hashedPassword = hash::make($data['password']);
        $check = Seller::create([
            'username' => $data['username'],
            'password' => $hashedPassword,
            'name' => $data['name'],
            'email' => $data['email'],
            'img' => $data['img'],
            'phone' => $data['phone'],
            'sex' => $data['sex'],
            'address' => $data['address'],
            'DOB' => $dob,
        ]);
        return redirect('adminSeller');
     } 
     public function viewAdminProfile(Request $request) {
        $adminId = $request->get('id');
        $admin = Admin::find($adminId);
        $dob = $admin->DOB;
        if($dob == null) {
            $dob = "1990-01-01";
        }
        $parts = explode('-', $dob);
        $year = $parts[0];
        $month = $parts[1];
        $day = $parts[2];
        return view('auth.account.adminProfile', [
            'admin' => $admin,
            'year' => $year,
            'month' => $month,
            'day' => $day
        ]);
     }
     public function updateAdminProfile(Request $request) {
        $input = $request->all();
        $dob = $request->get('year') . "-" . $request->get('month') . "-" . $request->get('day');
        $adminId = $request->get('id');
        $admin = Admin::find($adminId);
        $parts = explode('-', $dob);
        $year = $parts[0];
        $month = $parts[1];
        $day = $parts[2];
        $admin->name = $input['name'];
        $admin->username = $input['username'];
        $admin->email = $input['email'];
        $admin->phone = $input['phone'];
        $admin->address = $input['address'];
        $admin->sex =  $input['sex'];
        $admin->DOB =  $dob;
        session(['emailAdmin' => $admin->email]); 
        if ($request->hasFile('img')) {
            // Xóa hình ảnh cũ (nếu có)
            Storage::delete('img/img_auth/' . $admin->img);
    
            // Lưu hình ảnh mới vào thư mục lưu trữ
            $image = $request->file('img');
            $imageName = time().'.'.$image->getClientOriginalExtension();
            $image->move(public_path('img/img_auth'), $imageName);
    
            // Cập nhật tên hình ảnh mới cho sản phẩm
            $admin->img = $imageName;   
        }
        $admin->save();
        return view('auth.account.adminProfile', [
            'admin' => $admin,
            'year' => $year,
            'month' => $month,
            'day' => $day
        ]);
     }
     public function adminSellerDetail(Request $request) {
        $sellerId = $request->get('id');
        $seller = Seller::find($sellerId);
        $dob = $seller->DOB;
        if($dob == null) {
            $dob = "1990-01-01";
        }
        $parts = explode('-', $dob);
        $year = $parts[0];
        $month = $parts[1];
        $day = $parts[2];
        $products = Product::with('Category')->where('seller_id',$sellerId)->get();
        return view('auth.adminSellerDetail', [
            'seller' => $seller,
            'year' => $year,
            'month' => $month,
            'day' => $day,
            'products' => $products,
        ]);
     }
    public function signOut() {

        // xoa seesion hien tai va dang xuat
        Session::flush();
        Auth::logout();

        return Redirect('Login');
    }
}
    

