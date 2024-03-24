<?php
session_start();

class Cart {
    protected $cart_contents = array();

    public function __construct(){
        // Obtengo el array del carrito de la compra de la sesión
        $this->cart_contents = !empty($_SESSION['cart_contents'])?$_SESSION['cart_contents']:NULL;
        if ($this->cart_contents === NULL){
            // Valores base
            $this->cart_contents = array('cart_total' => 0, 'total_items' => 0);
        }
    }

    // Contenido del carrito
    public function contents(){
        //Pongo primero el mas nuevo
        $cart = array_reverse($this->cart_contents);

        // Elimino para que no tener problemas al mostrar la tabla
        unset($cart['total_items']);
        unset($cart['cart_total']);

        return $cart;
    }

    // Devuelve los detalles de un articulo específico
    public function get_item($row_id){
        return (in_array($row_id, array('total_items', 'cart_total'), TRUE) OR ! isset($this->cart_contents[$row_id]))
            ? FALSE
            : $this->cart_contents[$row_id];
    }

    // Devuelve el recuento total de articulos
    public function total_items(){
        return $this->cart_contents['total_items'];
    }

    // Devuelve el precio total
    public function total(){
        return $this->cart_contents['cart_total'];
    }

    // Incluyo los articulos en el carrito y guardo
    public function insert($item = array()){
        if(!is_array($item) OR count($item) === 0){
            return FALSE;
        }else{
            if(!isset($item['id'], $item['name'], $item['price'], $item['qty'])){
                return FALSE;
            }else{
                // Preparo cantidad
                $item['qty'] = (int) $item['qty'];
                if($item['qty'] <= 0){
                    return FALSE;
                }
                // Preparo precio
                $item['price'] = (float) $item['price'];
                // Creo un identificador unico para el articulo en el carrito
                $rowid = md5($item['id']);
                // Agrego la cantidad
                $old_qty = isset($this->cart_contents[$rowid]['qty']) ? (int) $this->cart_contents[$rowid]['qty'] : 0;
                // Entro con el identificador unico y la cantidad actualizada
                $item['rowid'] = $rowid;
                $item['qty'] += $old_qty;
                $this->cart_contents[$rowid] = $item;

                // Guardo el articulo
                if($this->save_cart()){
                    return isset($rowid) ? $rowid : TRUE;
                }else{
                    return FALSE;
                }
            }
        }
    }

    // Modificar el carrito
    public function update($item = array()){
        if (!is_array($item) OR count($item) === 0){
            return FALSE;
        }else{
            if (!isset($item['rowid'], $this->cart_contents[$item['rowid']])){
                return FALSE;
            }else{
                // Preparo cantidad
                if(isset($item['qty'])){
                    $item['qty'] = (int) $item['qty'];
                    // Borro articulo si la cantidad es cero
                    if ($item['qty'] == 0){
                        unset($this->cart_contents[$item['rowid']]);
                        return TRUE;
                    }
                }

                // Encuentro claves
                $keys = array_intersect(array_keys($this->cart_contents[$item['rowid']]), array_keys($item));
                // Preparo precio
                if(isset($item['price'])){
                    $item['price'] = (float) $item['price'];
                }
                foreach(array_diff($keys, array('id', 'name')) as $key){
                    $this->cart_contents[$item['rowid']][$key] = $item[$key];
                }
                // Guardo datos del carrito
                $this->save_cart();
                return TRUE;
            }
        }
    }

    // Guardar el array del carrito en la sesión
    protected function save_cart(){
        $this->cart_contents['total_items'] = $this->cart_contents['cart_total'] = 0;
        foreach ($this->cart_contents as $key => $val){
            if(!is_array($val) OR !isset($val['price'], $val['qty'])){
                continue;
            }

            $this->cart_contents['cart_total'] += ($val['price'] * $val['qty']);
            $this->cart_contents['total_items'] += $val['qty'];
            $this->cart_contents[$key]['subtotal'] = ($this->cart_contents[$key]['price'] * $this->cart_contents[$key]['qty']);
        }

        // Si el carro esta vacio lo elimino de la sesion
        if(count($this->cart_contents) <= 2){
            unset($_SESSION['cart_contents']);
            return FALSE;
        }else{
            $_SESSION['cart_contents'] = $this->cart_contents;
            return TRUE;
        }
    }

    //Borrar articulo del carrito
     public function remove($row_id){
        unset($this->cart_contents[$row_id]);
        $this->save_cart();
        return TRUE;
     }

    //Destruyo el carrito
    public function destroy(){
        $this->cart_contents = array('cart_total' => 0, 'total_items' => 0);
        unset($_SESSION['cart_contents']);
    }
}
?>