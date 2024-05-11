@extends('header_cart')

@section('content_cart')
<!-- CONTAINER -->
<div class="app__container payment__cart">
    <div class="grid">
        <div class="grid__row app__contents">
            <div class="grid__column-12">
                <div class="home-filter">
                    <span class="home-filter-title">Sắp xếp theo</span>
                    <button class="home-filter__btn btn">Phổ biến</button>
                    <button class="home-filter__btn btn btn--primary">Mới nhất</button>
                    <button class="home-filter__btn btn">Bán chạy</button>

                    <div class="select-input">
                        <span class="home-filter__label" for="">Giá</span>
                        <i class="search-icon fa-solid fa-angle-down"></i>

                        <!-- SELECT-INPUT-LIST -->
                        <ul class="select-input__list">
                            <li class="select-input__item">
                                <a href="" class="select-input__link">Giá: cao đến thấp</a>
                            </li>
                            <li class="select-input__item">
                                <a href="" class="select-input__link">Giá: thấp đến cao</a>
                            </li>
                        </ul>
                    </div>

                    <div class="home-filter__paginate">
                        <span class="home-filter__page-num">
                            <span class="page-current">1</span>
                            /14
                        </span>

                        <div class="home-filter__page-control">
                            <a href="" class="page-control-link page-control-link-icon-disabled">
                                <i class="page-control-link-icon fa-solid fa-angle-left"></i>
                            </a>
                            <a href="" class="page-control-link">
                                <i class="page-control-link-icon fa-solid fa-angle-right"></i>
                            </a>
                        </div>
                    </div>
                </div>
                <div class="home__product">
                    <div class="grid__row">
                        <!-- PRODUCT ITEM -->
                        <div class="grid__column-product_cart">
                            <table class="table__product-cart" id="customers">
                                <tr class="">
                                    <th>Tên sản phẩm</th>
                                    <th>Đơn Giá</th>
                                    <th>Số lượng</th>
                                    <th>Số tiền</th>
                                    <th>Thao tác</th>
                                </tr>
                                <tr class="product__cart-item">
                                    <td class="seller__td-img">
                                        <div class="detail__product-info">
                                            <img src="/img/img_auth/iphone-15.webp" alt="" class="seller-img_product">
                                            <a href="" class="information__product-link">
                                                <span class="seller-name_product">Iphone 13</span>
                                                <span class="seller-description_product">Mô tả chức năng</span>
                                            </a>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="price__product-cart">29900000</span>
                                        <span>đ</span>
                                    </td>
                                    <td>
                                        <div class="quantity__cart">
                                            <div class="sub_quantity">
                                                <i class="fa-solid fa-minus"></i>
                                            </div>
                                            <div class="number_quantity">1</div>
                                            <div class="add_quantity">
                                                <i class="fa-solid fa-plus"></i>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="total__product-cart">29900000</span>
                                        <span>đ</span>
                                    </td>
                                    <td class="action__product">
                                        <a href="" class="seller__product-delete">Xóa</a>
                                    </td>
                                </tr>
                                <tr class="product__cart-item">
                                    <td class="seller__td-img">
                                        <div class="detail__product-info">
                                            <img src="/img/img_auth/iphone-15.webp" alt="" class="seller-img_product">
                                            <a href="" class="information__product-link">
                                                <span class="seller-name_product">Iphone 13</span>
                                                <span class="seller-description_product">Mô tả chức năng</span>
                                            </a>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="price__product-cart">29900000</span>
                                        <span>đ</span>
                                    </td>
                                    <td>
                                        <div class="quantity__cart">
                                            <div class="sub_quantity">
                                                <i class="fa-solid fa-minus"></i>
                                            </div>
                                            <div class="number_quantity">1</div>
                                            <div class="add_quantity">
                                                <i class="fa-solid fa-plus"></i>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="total__product-cart">29900000</span>
                                        <span>đ</span>
                                    </td>
                                    <td class="action__product">
                                        <a href="" class="seller__product-delete">Xóa</a>
                                    </td>
                                </tr>
                                <tr class="product__cart-item">
                                    <td class="seller__td-img">
                                        <div class="detail__product-info">
                                            <img src="/img/img_auth/iphone-15.webp" alt="" class="seller-img_product">
                                            <a href="" class="information__product-link">
                                                <span class="seller-name_product">Iphone 13</span>
                                                <span class="seller-description_product">Mô tả chức năng</span>
                                            </a>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="price__product-cart">29900000</span>
                                        <span>đ</span>
                                    </td>
                                    <td>
                                        <div class="quantity__cart">
                                            <div class="sub_quantity">
                                                <i class="fa-solid fa-minus"></i>
                                            </div>
                                            <div class="number_quantity">1</div>
                                            <div class="add_quantity">
                                                <i class="fa-solid fa-plus"></i>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="total__product-cart">29900000</span>
                                        <span>đ</span>
                                    </td>
                                    <td class="action__product">
                                        <a href="" class="seller__product-delete">Xóa</a>
                                    </td>
                                </tr>
                                <tr class="product__cart-item">
                                    <td class="seller__td-img">
                                        <div class="detail__product-info">
                                            <img src="/img/img_auth/iphone-15.webp" alt="" class="seller-img_product">
                                            <a href="" class="information__product-link">
                                                <span class="seller-name_product">Iphone 13</span>
                                                <span class="seller-description_product">Mô tả chức năng</span>
                                            </a>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="price__product-cart">29900000</span>
                                        <span>đ</span>
                                    </td>
                                    <td>
                                        <div class="quantity__cart">
                                            <div class="sub_quantity">
                                                <i class="fa-solid fa-minus"></i>
                                            </div>
                                            <div class="number_quantity">1</div>
                                            <div class="add_quantity">
                                                <i class="fa-solid fa-plus"></i>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="total__product-cart">29900000</span>
                                        <span>đ</span>
                                    </td>
                                    <td class="action__product">
                                        <a href="" class="seller__product-delete">Xóa</a>
                                    </td>
                                </tr>
                                <tr class="product__cart-item">
                                    <td class="seller__td-img">
                                        <div class="detail__product-info">
                                            <img src="/img/img_auth/iphone-15.webp" alt="" class="seller-img_product">
                                            <a href="" class="information__product-link">
                                                <span class="seller-name_product">Iphone 13</span>
                                                <span class="seller-description_product">Mô tả chức năng</span>
                                            </a>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="price__product-cart">29900000</span>
                                        <span>đ</span>
                                    </td>
                                    <td>
                                        <div class="quantity__cart">
                                            <div class="sub_quantity">
                                                <i class="fa-solid fa-minus"></i>
                                            </div>
                                            <div class="number_quantity">1</div>
                                            <div class="add_quantity">
                                                <i class="fa-solid fa-plus"></i>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="total__product-cart">29900000</span>
                                        <span>đ</span>
                                    </td>
                                    <td class="action__product">
                                        <a href="" class="seller__product-delete">Xóa</a>
                                    </td>
                                </tr>
                                <tr class="product__cart-item">
                                    <td class="seller__td-img">
                                        <div class="detail__product-info">
                                            <img src="/img/img_auth/iphone-15.webp" alt="" class="seller-img_product">
                                            <a href="" class="information__product-link">
                                                <span class="seller-name_product">Iphone 13</span>
                                                <span class="seller-description_product">Mô tả chức năng</span>
                                            </a>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="price__product-cart">29900000</span>
                                        <span>đ</span>
                                    </td>
                                    <td>
                                        <div class="quantity__cart">
                                            <div class="sub_quantity">
                                                <i class="fa-solid fa-minus"></i>
                                            </div>
                                            <div class="number_quantity">1</div>
                                            <div class="add_quantity">
                                                <i class="fa-solid fa-plus"></i>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="total__product-cart">29900000</span>
                                        <span>đ</span>
                                    </td>
                                    <td class="action__product">
                                        <a href="" class="seller__product-delete">Xóa</a>
                                    </td>
                                </tr>
                                <tr class="product__cart-item">
                                    <td class="seller__td-img">
                                        <div class="detail__product-info">
                                            <img src="/img/img_auth/iphone-15.webp" alt="" class="seller-img_product">
                                            <a href="" class="information__product-link">
                                                <span class="seller-name_product">Iphone 13</span>
                                                <span class="seller-description_product">Mô tả chức năng</span>
                                            </a>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="price__product-cart">29900000</span>
                                        <span>đ</span>
                                    </td>
                                    <td>
                                        <div class="quantity__cart">
                                            <div class="sub_quantity">
                                                <i class="fa-solid fa-minus"></i>
                                            </div>
                                            <div class="number_quantity">1</div>
                                            <div class="add_quantity">
                                                <i class="fa-solid fa-plus"></i>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="total__product-cart">29900000</span>
                                        <span>đ</span>
                                    </td>
                                    <td class="action__product">
                                        <a href="" class="seller__product-delete">Xóa</a>
                                    </td>
                                </tr>
                            </table>

                        </div>
                        <!-- PRODUCT ITEM -->
                        <div class="grid payment__cart-item">
                            <div class="action__cart">
                                <div class="payment__cart-title">
                                    Tổng tiền:
                                    <span class="price_total-payment">25.999.000 <span>đ</span></span>
                                </div>
                                <div class="payment__cart-btn">
                                    <button class="btn btn--primary">Thanh toán</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
            </div>
        </div>

    </div>

</div>
@endsection