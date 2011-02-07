<?php
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2010 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 */

// {{{ requires
require_once(CLASS_REALDIR . "pages/frontparts/bloc/LC_Page_FrontParts_Bloc.php");

/**
 * カート のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 * @version $Id:LC_Page_FrontParts_Bloc_Cart.php 15532 2007-08-31 14:39:46Z nanasess $
 */
class LC_Page_FrontParts_Bloc_Cart extends LC_Page_FrontParts_Bloc {

    // }}}
    // {{{ functions

    /**
     * Page を初期化する.
     *
     * @return void
     */
    function init() {
        parent::init();
        $this->setTplMainpage('cart.tpl');
    }

    /**
     * Page のプロセス.
     *
     * @return void
     */
    function process() {
        $this->action();
        $this->sendResponse();
    }

    /**
     * Page のアクション.
     *
     * @return void
     */
    function action() {
        $objCart = new SC_CartSession();
        $objSiteInfo = new SC_SiteInfo;
        $this->isMultiple = $objCart->isMultiple();
        $this->arrCartList = $this->lfGetCartData($objCart, $objSiteInfo);
    }

    /**
     * デストラクタ.
     *
     * @return void
     */
    function destroy() {
        parent::destroy();
    }

    /**
     * カートの情報を取得する
     *
     * @param SC_CartSession $objCart カートセッション管理クラス
     * @param SC_SiteInfo $objSiteInfo サイト情報クラス
     * @return array $arrCartList カートデータ配列
     */
    function lfGetCartData(&$objCart, &$objSiteInfo) {
        $arrCartKeys = $objCart->getKeys();
        foreach ($arrCartKeys as $cart_key) {
            // カート情報を取得
            $arrCartList = $objCart->getCartList($cart_key);
            // カート内の商品ＩＤ一覧を取得
            $arrAllProductID = $objCart->getAllProductID($cart_key);
            // 商品が1つ以上入っている場合には商品名称を取得
            if (count($arrCartList) > 0){
                
                foreach($arrCartList['productsClass'] as $key => $val){
                    $arrCartList[$key]['product_name'] = $val['name'];
                }
            }
            // 購入金額合計
            $products_total += $objCart->getAllProductsTotal($cart_key);
            // 合計数量
            $total_quantity += $objCart->getTotalQuantity($cart_key);
        }
        
        // 店舗情報の取得
        $arrInfo = $objSiteInfo->data;
        
        // 送料無料までの金額
        $arrCartList[0]['ProductsTotal'] = $products_total;
        $arrCartList[0]['TotalQuantity'] = $total_quantity;
        
        $deliv_free = $arrInfo['free_rule'] - $products_total;
        $arrCartList[0]['free_rule'] = $arrInfo['free_rule'];
        $arrCartList[0]['deliv_free'] = $deliv_free;
        
        return $arrCartList;
        
    }

}
?>
