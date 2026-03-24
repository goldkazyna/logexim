<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Ajax_model extends CI_Model {
    function getMainID($alias){
        $this->db->where('alias',$alias);
        $query = $this->db->get('tm_main_category');
        $result= $query->row();
        return $result->id;
    }
    function getCatID($alias){
        $this->db->where('alias',$alias);
        $query = $this->db->get('tm_category');
        $result= $query->row();
        return $result->id;
    }

     function getAliasCatID($id_main){
        $this->db->where('id_main_category',$id_main);
        $query = $this->db->get('tm_category');
	$ids = array();
	$result = $query->result();
	foreach($result as $item){
		$ids[] = $item->id;
	}
        return  $ids;
    }
     function getCatID_2($alias, $id_main){
        $this->db->where('alias',$alias);
        $this->db->where('id_main_category',$id_main);
        $query = $this->db->get('tm_category');
        $result= $query->row();
        return $result->id;
    }
    function getSubCatID($id,$alias){
        /*var_dump($id,$alias); в alias передается ajax */
        $this->db->where('id_category',$id);
        $this->db->where('alias',$alias);
        $query = $this->db->get('tm_subcategory');
        $result= $query->row();
        return $result->id;
    }

    function get_cover_img_BY_id($id_product){
        $this->db->where('id_product',$id_product);
        $this->db->where('cover',1);
        $query = $this->db->get('tm_gallery');
        return $query->row_array();
    }
    function get_cover_img_col_BY_id($id_product){
        $this->db->where('id_product_col',$id_product);
        $this->db->where('cover',1);
        $query = $this->db->get('tm_gallery_col');
        return $query->row_array();
    }
    function getAttr($cID){

	 if(!is_array($cID)){

			$this->db->where('id_category',$cID);
		}else{

			$this->db->where_in('id_category',$cID);
		}
        //$this->db->where('id_category',$cID);
        $this->db->order_by('id','asc');
	//$this->db->order_by('title','asc');
        $query = $this->db->get('tm_attr_category');
        if($query->num_rows()==0) return false;
        $res= $query->result();

        foreach($res as $item):

            	$result['ATTR'][$item->id]["NAME"]= $item->title;

            $result['IDS'][]= $item->id;
        endforeach;

        $this->db->where_in('id_attr_category',$result['IDS']);
       // $this->db->order_by('id','asc');
	$this->db->order_by('title','asc');
        $query = $this->db->get('tm_attr_value');
        $res= $query->result();

        foreach($res as $item):

            	$result['ATTR'][$item->id_attr_category]['VALUES'][$item->id]= $item->title;

        endforeach;
	 if(is_array($cID)){
		$tmp = array();
		foreach($result['ATTR'] as $id_attr_category => $ars){
			$ars['ID'] = $id_attr_category;
			$tmp['ATTR'][$ars['NAME']][] = $ars;
		}
		$result = $tmp;
	}

        return $result;
    }

	function getIdsProductSubCategory($scID){
		$this->db->select('id_product');
		 if(!empty($scID)) {
		    if(!is_array($scID)){
			$this->db->where('id_subcategory',$scID);
		    }else{
			$this->db->where_in('id_subcategory',$scID);
		    }
   			$query = $this->db->get('tm_subcategory_to_product');
			$res = $query->result();
			$ids = array();
			if($res > 0){
				foreach($res as $item){
					$ids[] = $item->id_product;
				}
				return  array('ids_product' => $ids);
				//var_dump($ids);
			}


		}
		return 0;

	}

	function getAttributeSubCategory($scID){
		$query = $this->db->get_where('tm_attr_category',array('id_subcategory' => $scID));
		$attr_category = $query->result();
		$attrs = array();
		foreach($attr_category as $item){
				$values = array();
				$query = $this->db->get_where('tm_attr_value',array('id_attr_category' => $item->id));
				$res = $query->result();
				foreach($res as $it){
					$values[] = array('title' => $it->title, 'id' => $it->id);
				}
			$attrs[] = array('title' => $item->title, 'values' => $values);
		}
		return $attrs;

	}

    function getProductList($cID,$scID=null,$attr=array(),$price=array(),$sort="title",$page=0,$ll=1024, $available=0){
        if(isset($attr))
            if(count($attr)):

		//var_dump($attr);
		/*$alls_attr = array();

		 foreach($attr as $attrID=>$attrValue){
			$alls_attr = array_merge($alls_attr, $attrValue);
		}
		sort($alls_attr);*/
		$tmp_ids = array();
                foreach($attr as $attrID=>$attrValue):
				//  var_dump('$attr', $attrValue);

                    $this->db->select('id_product');
                    $this->db->where_in('id_value',$attrValue);
                    $query = $this->db->get('tm_attr_to_product');
                    $res= $query->result();

                    $tmp= array();
                    foreach($res as $val):
                        $tmp[]= $val->id_product;

                        $status= 0;
                    endforeach;
                    if(empty($IDs)){
                        $IDs= $tmp;
			//$tmp_ids[count($IDs)][$attrID][] =  $IDs;
			$tmp_ids[$attrID] = $IDs;
			//var_dump(count($IDs));
			//echo '<br>';
                    }else{
                        //var_dump($IDs, $tmp);
			            //echo '<br>';
                        //$IDs = array_intersect($IDs, $tmp);
                         $IDs = array_merge($IDs, $tmp);
			//$tmp_ids[count($IDs)][$attrID][] =  $tmp;
			$tmp_ids[$attrID] = $tmp;



			}
                endforeach;
                if(empty($IDs)) $IDs= array(-1);

		if(count($tmp_ids) > 1){

			$new_ids = array();
			//var_dump('IDs', $IDs );
			//var_dump('tmp_ids', $tmp_ids );
			foreach($IDs as $ids){

				$flag = true;

				foreach($tmp_ids as $arr){

					if(!in_array($ids, $arr)){
						$flag = false;
					}
				}


				if($flag && !in_array($ids, $new_ids)){

					$new_ids[] = $ids;
				}
			}
			if(count($new_ids) > 0){
				$IDs = $new_ids;
			}else{
				$IDs  = array(0);
			}



		}



            endif;
        if ($available!=0)
            $this->db->where('count >=',1);
        if(isset($price['min']))
            $this->db->where('price >=',$price['min']);
        if(isset($price['max']))
            $this->db->where('price <=',$price['max']);
	if(!is_array($cID)){
		$this->db->where('id_category',$cID);
	}else{
		$this->db->where_in('id_category',$cID);
	}
        if(!empty($scID)) {
		if(isset($scID['ids_product'])){

			  $this->db->where_in('id', $scID['ids_product']);
		}else{
		    if(!is_array($scID))
			$this->db->where('id_subcategory',$scID);
		    else
			$this->db->where_in('id_subcategory',$scID);
		}
        }
        if(is_array(@$IDs))
            $this->db->where_in('id',$IDs);
        //$this->db->where('id_category',$cID);
        //$this->db->where('id_subcategory',$scID);


        $this->db->where('visible',1);

        $query = $this->db->get('tm_product');
        $pages= Ceil($query->num_rows()/$ll);
        $res['viewOptions']= array("count_product"=> $query->num_rows(),"pages"=>$pages,"current"=>$page,"sort"=>$sort, "available"=>$available);


        if ($available!=0){

            $this->db->where('count >=',1);
            }


        if(isset($price['min'])){
            $this->db->where('price >=',$price['min']);

}
        if(isset($price['max']))
            $this->db->where('price <=',$price['max']);
        $this->db->order_by($sort);
        if(!is_array($cID)){
		$this->db->where('id_category',$cID);
	}else{
		$this->db->where_in('id_category',$cID);
	}
        if(!empty($scID)) {
		if(isset($scID['ids_product'])){
			  $this->db->where_in('id', $scID['ids_product']);
		}else{
		    if(!is_array($scID))
		        $this->db->where('id_subcategory',$scID);
		    else
		        $this->db->where_in('id_subcategory',$scID);
		}
        }
        if(is_array(@$IDs))
            $this->db->where_in('id',$IDs);
        //$this->db->where('id_category',$cID);
        //$this->db->where('id_subcategory',$scID);
        $this->db->where('visible',1);
         // var_dump( $this->db);
        if(!empty($ll))
            $this->db->limit($ll,$page*$ll);
        $query = $this->db->get('tm_product');
        $res['products']= $query->result();

        return $res;
    }


function searchCityDelivery($search){
    $this->db->like('title',"$search");
    $query = $this->db->get('city_delivery');
    return $query->result();


}
function searchTrack($search){
     $this->db->where('track_number',$search);
    $query = $this->db->get('tm_order');
    return $query->result();


}
function calcDeliveryAir($from, $to){
    $this->db->where('city_from',$from);
    $this->db->where('city_to',$to);
    $query = $this->db->get('avia');
    return $query->result();


}
function calcDeliveryZd($from, $to){
    $this->db->where('city_from',$from);
    $this->db->where('city_to',$to);
    $query = $this->db->get('zh');
    return $query->result();


}
function calcDeliveryCar($from, $to){
    $this->db->where('city_from',$from);
    $this->db->where('city_to',$to);
    $query = $this->db->get('avto');
    return $query->result();


}





	 function getProductListMain($cID,$scID=null,$attr=array(),$price=array(),$sort="title",$page=0,$ll=1024, $available=0){

        if(isset($attr))
            if(count($attr)):

		/*$alls_attr = array();

		 foreach($attr as $attrID=>$attrValue){
			$alls_attr = array_merge($alls_attr, $attrValue);

		}
		sort($alls_attr);
		$tmp_ids = array();*/

                foreach($attr as $attrID=>$attrValue):

		    $this->db->select('id_attr_category,id');
		    $this->db->where_in('title',$attrValue);
		    $query = $this->db->get('tm_attr_value');
                    $res= $query->result();
		    $ids_tm_attr_to_product = array();
			foreach($res as $item){
				$ids_tm_attr_to_product[] =  $item->id;
			}
			if(count($ids_tm_attr_to_product) === 0) continue;
		   // var_dump($attrValue,  $res);die();

                    $this->db->select('id_product');
                    $this->db->where_in('id_value',$ids_tm_attr_to_product);
                    $query = $this->db->get('tm_attr_to_product');
                    $res= $query->result();
                    $tmp= array();
                    foreach($res as $val):
                        $tmp[]= $val->id_product;
                        $status= 0;
                    endforeach;
                    if(empty($IDs)){
                        $IDs= $tmp;
			//$tmp_ids[count($IDs)][$attrID][] =  $IDs;
			$tmp_ids[$attrID] = $IDs;
			//var_dump(count($IDs));
			//echo '<br>';
                    }else{
                       // $IDs = array_intersect($IDs, $tmp);
                         $IDs = array_merge($IDs, $tmp);
			//$tmp_ids[count($IDs)][$attrID][] =  $tmp;
			$tmp_ids[$attrID] = $tmp;
			//var_dump(count($IDs));
			//echo '<br>';
			}
                endforeach;
                if(empty($IDs)) $IDs= array(-1);
		if(count($tmp_ids) > 1){

			$new_ids = array();
			//var_dump('IDs', $IDs );
			//var_dump('tmp_ids', $tmp_ids );
			foreach($IDs as $ids){

				$flag = true;

				foreach($tmp_ids as $arr){

					//var_dump($arr);
					if(!in_array($ids, $arr)){
						$flag = false;
					}
				}


				if($flag && !in_array($ids, $new_ids)){

					$new_ids[] = $ids;
				}
			}

			if(count($new_ids) > 0){
				$IDs = $new_ids;
			}else{
				$IDs  = array(0);
			}



		}
            endif;


        if ($available!=0)
            $this->db->where('count >=',1);
        if(isset($price['min']))
            $this->db->where('price >=',$price['min']);
        if(isset($price['max']))
            $this->db->where('price <=',$price['max']);
	if(!is_array($cID)){
		$this->db->where('id_category',$cID);
	}else{
		$this->db->where_in('id_category',$cID);
	}
        if(!empty($scID)) {
		if(isset($scID['ids_product'])){

			  $this->db->where_in('id', $scID['ids_product']);
		}else{
		    if(!is_array($scID))
			$this->db->where('id_subcategory',$scID);
		    else
			$this->db->where_in('id_subcategory',$scID);
		}
        }
        if(is_array(@$IDs))
            $this->db->where_in('id',$IDs);
        //$this->db->where('id_category',$cID);
        //$this->db->where('id_subcategory',$scID);


        $this->db->where('visible',1);

        $query = $this->db->get('tm_product');
        $pages= Ceil($query->num_rows()/$ll);
        $res['viewOptions']= array("count_product"=> $query->num_rows(),"pages"=>$pages,"current"=>$page,"sort"=>$sort, "available"=>$available);


        if ($available!=0){

            $this->db->where('count >=',1);
            }


        if(isset($price['min'])){
            $this->db->where('price >=',$price['min']);

}
        if(isset($price['max']))
            $this->db->where('price <=',$price['max']);
        $this->db->order_by($sort);
        if(!is_array($cID)){
		$this->db->where('id_category',$cID);
	}else{
		$this->db->where_in('id_category',$cID);
	}
        if(!empty($scID)) {
		if(isset($scID['ids_product'])){
			  $this->db->where_in('id', $scID['ids_product']);
		}else{
		    if(!is_array($scID))
		        $this->db->where('id_subcategory',$scID);
		    else
		        $this->db->where_in('id_subcategory',$scID);
		}
        }
        if(is_array(@$IDs))
            $this->db->where_in('id',$IDs);
        //$this->db->where('id_category',$cID);
        //$this->db->where('id_subcategory',$scID);
        $this->db->where('visible',1);
         // var_dump( $this->db);
        if(!empty($ll))
            $this->db->limit($ll,$page*$ll);
        $query = $this->db->get('tm_product');
        $res['products']= $query->result();

        return $res;
    }


    function getFilterFromGet_brand($get){
        foreach($get as $key=>$value):
            if(stripos("1".$key,"brand_")):
                $key= str_replace("brand_","",$key)*1;
                $attr[$key][]= $value;
            endif;
        endforeach;
        return @$attr;
    }
    function getFilterFromGet($get){

        foreach($get as $key=>$value):
            if(stripos("1".$key,"attr_")):
			//var_dump($value);
                $key= str_replace("attr_","",$key)*1;
		 if(!is_array($value)){
                	$attr[$key][]= $value;
		}else{
			$attr[$key]= $value;
		}
            endif;
        endforeach;
        return @$attr;
    }
    function FastSearch($serachString){
        $this->db->like('title',"$serachString");
        $this->db->limit(5,0);
        $query = $this->db->get('brand');
        $res['brand']= $query->result();
        $this->db->like('articul',"$serachString");
        $this->db->limit(5,0);
        $query = $this->db->get('tm_product');
        $res['articul']= $query->result();
        $this->db->like('title',"$serachString");
        $this->db->limit(5,0);
        $this->db->where('visible',1);
        $query = $this->db->get('tm_product');
        $res['products']= $query->result();
        $this->db->like('title',"$serachString");
        $this->db->limit(5,0);
        $query = $this->db->get('tm_product_col');
        $res['products_col']= $query->result();
        $this->db->like('articul',"$serachString");
        $this->db->limit(5,0);
        $query = $this->db->get('tm_product_col');
        $res['articul_col']= $query->result();
        return $res;
    }
    function FastSearchcity($serachString){
        $this->db->like('title',"$serachString");
        $this->db->limit(5,0);
        $query = $this->db->get('list_city');
        $res['list_city']= $query->result();

        return $res;
    }
    function newFastSearch($serachString){
        $this->db->like('title',"$serachString");
        $this->db->limit(5,0);
        $query = $this->db->get('brand');
        $res['brand']= $query->result();
        $this->db->like('articul',"$serachString");
        $this->db->limit(5,0);
        $query = $this->db->get('tm_product');
        $res['articul']= $query->result();
        $this->db->like('title',"$serachString");
        $this->db->limit(5,0);
        $this->db->where('visible',1);
        $query = $this->db->get('tm_product');
        $res['products']= $query->result();
        $this->db->like('title',"$serachString");
        $this->db->limit(5,0);
        $query = $this->db->get('tm_product_col');
        $res['products_col']= $query->result();
        $this->db->like('articul',"$serachString");
        $this->db->limit(5,0);
        $query = $this->db->get('tm_product_col');
        $res['articul_col']= $query->result();
        return $res;
    }
    function newHot(){
        $this->db->where('hot',1);
        $query = $this->db->get('tm_product');
        $res['products']= $query->result();
        return $res;
    }

    function newFastSearchMore($serachString,$startFrom){
        $this->db->like('title',"$serachString");
        $this->db->limit(10,$startFrom);
        $this->db->where('visible',1);
        $query = $this->db->get('tm_product');
        $res['products']= $query->result();
        return $res;
    }

    function uploadFiles($photos){
        $uploadPrefix= $this->session->userdata("uploadPrefix");
        if(!is_array($photos['tmp_name']))
            return false;
        $list= scandir("images/products/temp");
        $count= 0;
        foreach($list as $key=>$file):
            if($key<2) continue;
            if(stripos($file,"".$uploadPrefix)):
                $count++;
            endif;
        endforeach;
        if($count<4)
            foreach($photos['tmp_name'] as $key=>$tmpName):
                $count++;
                if($count>4) break;
                move_uploaded_file($tmpName,"images/products/temp/photo_".$uploadPrefix."_".$count.".".str_replace("image/","",$photos['type'][$key]));
            endforeach;
        $list= scandir("images/products/temp");
        $result= array();
        foreach($list as $key=>$file):
            if($key<2) continue;
            if(stripos($file,"".$uploadPrefix)):
                $result[]="/images/products/temp/".$file;
            endif;
        endforeach;
       if(count($result));
            echo json_encode($result);
    }

    function getPriceMin($catID, $isSubCategory = true){

        if($isSubCategory){
		if(isset($catID['ids_product'])){
			  $this->db->where_in('id', $catID['ids_product']);
		}else{
            		$this->db->where('id_subcategory',$catID);
		}
        }else
		if(!is_array($catID)){
			$this->db->where('id_category',$catID);
		}else{
			$this->db->where_in('id_category',$catID);
		}
           // $this->db->where('id_category',$catID);
        $this->db->order_by('price',"asc");
        $query = $this->db->get('tm_product');
        if($query->num_rows()==0) return 0;
        $price= $query->row();
        return $price->price;
    }

    function getPriceMax($catID, $isSubCategory = true){

        if($isSubCategory){
		if(isset($catID['ids_product'])){
			  $this->db->where_in('id', $catID['ids_product']);
		}else{
            		$this->db->where('id_subcategory',$catID);
		}
        }else
            if(!is_array($catID)){
			$this->db->where('id_category',$catID);
		}else{
			$this->db->where_in('id_category',$catID);
		}
           // $this->db->where('id_category',$catID);
        $this->db->order_by('price',"desc");
        $query = $this->db->get('tm_product');
        if($query->num_rows()==0) return 0;
        $price= $query->row();
        return $price->price;
    }

    function update_imgGalleryCover($id){
        $this->db->where('id',$id);
        $query = $this->db->get('tm_gallery');
        $result= $query->row();
        $productID= $result->id_product;

        $this->db->where('id_product',$productID);
        $this->db->update('tm_gallery',array("cover"=>0));

        $this->db->where('id',$id);
        $this->db->update('tm_gallery',array("cover"=>1));
    }





    function fastSearchArticle($search){
        $this->db->limit(10);
        $this->db->select('*');
        $this->db->from('tm_product');
        $this->db->like('articul',$search);
        $this->db->order_by('articul','asc');
        $query=$this->db->get();
        return $query->result_array();



    }

    function getBrandId($brand){
	$this->db->where('alias',$brand);
        $query = $this->db->get('brand');
        if($query->num_rows()==0) return 0;
        $id= $query->row();
        return $id;
    }

     function getProductWhereBrand($brand_id){
        $this->db->where('brand',$brand_id);
        $this->db->order_by('price',"asc");
        $query = $this->db->get('tm_product');
      	return $query->result();
    }

    function getProductColWhereIds($ids_tm_products){
        $this->db->where_in('id_col', $ids_tm_products);
        $this->db->order_by('price',"asc");
        $query = $this->db->get('tm_product_col');
      	return $query->result();
    }



    function getBrandPriceMin($brand){
        $this->db->where('brand',$brand);
        $this->db->order_by('price',"asc");
        $query = $this->db->get('tm_product');
        if($query->num_rows()==0) return 0;
        $price= $query->row();
        return $price->price;
    }

    function getBrandPriceMax($brand){
        $this->db->where('brand',$brand);
        $this->db->order_by('price',"desc");
        $query = $this->db->get('tm_product');
        if($query->num_rows()==0) return 0;
        $price= $query->row();
        return $price->price;
    }

    function getBrandProductList($brand,$attr=array(),$price=array(),$sort="title",$page=0,$ll=12){
        if(isset($attr))
            if(count($attr)):
                $categsId = array();
                foreach($attr[0] as $attrID=>$attrValue):
                    $categsId[] = $attrValue;
				endforeach;
                /*
                $this->db->select('id');
                $this->db->where_in('id_subcategory',$attr[0][0]);
                $query = $this->db->get('tm_product');
                $res= $query->result();
                $tmp= array();
                foreach($res as $val):
                    $tmp[]= $val->id;
                    $status= 0;
                endforeach;
                if(empty($IDs))
                    $IDs= $tmp;
                else
                    $IDs = array_intersect($IDs, $tmp);
				*/
            endif;
			//return $categsId;
        if(isset($price['min']))
            $this->db->where('price >=',$price['min']);
        if(isset($price['max']))
            $this->db->where('price <=',$price['max']);
        $this->db->where('brand',$brand);

        if(is_array(@$categsId))
            $this->db->where_in('id_subcategory',$categsId);
        $this->db->where('brand',$brand);
        $this->db->where('visible',1);
        $query = $this->db->get('tm_product');
        $pages= Ceil($query->num_rows()/$ll);
        $res['viewOptions']= array("pages"=>$pages,"current"=>$page,"sort"=>$sort);

        if(isset($price['min']))
            $this->db->where('price >=',$price['min']);
        if(isset($price['max']))
            $this->db->where('price <=',$price['max']);
        $this->db->order_by($sort);
        $this->db->where('brand',$brand);
        if(is_array($categsId))
            $this->db->where_in('id_subcategory',$categsId);
        $this->db->where('brand',$brand);
        $this->db->where('visible',1);
        if(!empty($ll))
            $this->db->limit($ll,$page*$ll);
        $query = $this->db->get('tm_product');
        $res['products']= $query->result();

        return $res;
    }

    function getBrandAttr($brand){
        $this->db->select("id_subcategory");
        $this->db->where('brand',$brand);
        $this->db->where('visible',1);

        $this->db->group_by("id_subcategory");
        $query = $this->db->get('tm_product');
        if($query->num_rows()==0) return false;
        $res= $query->result();
        $result['ATTR']['id_subcategory']["NAME"]= '���������';
        $result['IDS'][]= 1;
        foreach($res as $item):
            $scat = $this->db->where('id',$item->id_subcategory)->get('tm_subcategory')->row();

            $result['ATTR']['id_subcategory']['VALUES'][$scat->id]= $scat->title;

        endforeach;

        $this->db->where_in('id_attr_category',$result['IDS']);
        $this->db->order_by('id','asc');
        $query = $this->db->get('tm_attr_value');
        $res= $query->result();
        foreach($res as $item):
            $result['ATTR'][$item->id_attr_category]['VALUES'][$item->id]= $item->title;
        endforeach;

        return $result;
    }
}
