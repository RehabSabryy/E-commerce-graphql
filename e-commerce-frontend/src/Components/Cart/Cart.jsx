import React, { Component } from 'react';
import Style from "./Cart.module.css";
import { CartContext } from '../../Context/CartContext';
import { gql } from "@apollo/client";
import client from '../Graphql/client';

// Place order mutation 
const PlaceOrder = gql`
mutation createOrder($order: OrderInput!) {
  createOrder(order: $order) {
    quantity
    price
    product_id
    attributes {
      id
      attribute_name
      item_id
      item_value
      item_display_value
    }
  }
}`;



export default class Cart extends Component {
  static contextType = CartContext;

  incrementProduct = (productId) => {
    const { productsInCart, updateCart } = this.context;
    const updatedProducts = productsInCart.map((item) => {
      if (item.id === productId) {
        return { ...item, quantity: item.quantity + 1 };
      }
      return item;
    });
    updateCart(updatedProducts);
  }

  decrementProduct = (productId) => {
    const { productsInCart, updateCart } = this.context;
    const updatedProducts = productsInCart.reduce((acc, item) => {
      if (item.id === productId) {
        if (item.quantity > 1) {
          acc.push({ ...item, quantity: item.quantity - 1 });
        }
      } else {
        acc.push(item);
      }
      return acc;
    }, []);
    updateCart(updatedProducts);
  }
  placeOrder = async () => {
    const { productsInCart, updateCart } = this.context;
    console.log("Products in Cart:", productsInCart);
  
    for (const product of productsInCart) {
      const orderInput = {
        quantity: product.quantity,
        price: product.prices && product.prices.length > 0 ? product.prices[0].amount : 0,
        product_id: product.id,
        attributes: product.attributes.map((attribute) => ({
          id: attribute.id,
          attribute_name: attribute.name,
          item_id: attribute.selectedItem.id,
          item_value: attribute.selectedItem.value,
          item_display_value: attribute.selectedItem.displayValue,
        })),
      };
  
      console.log("Order Input:", JSON.stringify(orderInput, null, 2));
  
      try {
        const result = await client.mutate({
          mutation: PlaceOrder,
          variables: { order: orderInput }
        });
        console.log(result.data.createOrder);
        // remove from local storage 
        localStorage.removeItem("cart");
        updateCart([]); // Clear the cart in the contex
      } catch (error) {
        console.error("Error placing order:", error);
      }
    }
  }
  


  render() {
    const {  productsInCart } = this.context;
    console.log(this.context);
    const {toggleCart,isCartOpen, cartCount} = this.props;

    return (
      <>
        {isCartOpen && (
          <>
            <div className={`${Style.overlay}`} onClick={toggleCart}></div>
            <div className={`${Style.cartPopover} d-flex flex-column justify-content-between`} data-testid='cart-item-attribute-${attribute name in kebab case}'>
              <div className="cartTitle d-flex flex-row p-3">
                <h3 className="fs-5 me-1">My bag,</h3>
                <div>
                  {cartCount === 1 ? <p className="fs-6">Item: 1</p> :
                    <p className="fs-6">Items: {cartCount}</p>}
                </div>
              </div>
              <div className="eachItem d-flex flex-column">
                {productsInCart.map((product) => (
                  <div key={product.id} className="allItem d-flex flex-row pt-3">
                    <div className={`leftPart pe-2 ${Style.leftPart}`}>
                      <p className={`${Style.productName} ${Style.itemSize}`}>{product.name}</p>
                      <p className={`fw-bold ${Style.itemSize}`}>{product.prices[0].currency.symbol}{(product.prices[0].amount * product.quantity).toFixed(2)}</p>
                      {product.attributes && product.attributes.map((attribute, index) => (
                        <div key={index} className={Style.itemSize}>
                          <p className={Style.itemSize}>{attribute.name} :</p>
                          <span className={`d-flex flex-wrap`}>
                            {attribute.items.map((item, idx) => (
                              <div
                                key={idx}
                                className={`border m-1 p-0 ${Style.itemSize}`}
                                style={{
                                  backgroundColor: attribute.selectedItem && attribute.selectedItem.value === item.value ? 'black' : 'white',
                                  color: attribute.selectedItem && attribute.selectedItem.value === item.value ? 'white' : 'black'
                                }}
                              >
                                {attribute.name === "Color" ? (
                                  <div className={`${Style.color}`} style={{ backgroundColor: item.value, borderColor: attribute.selectedItem && attribute.selectedItem.value === item.value ? 'red' : 'transparent' }}></div>
                                ) :
                                      <p className={`p-1 m-0 ${Style.fSize}`} style={{
                                        color: attribute.selectedItem && attribute.selectedItem.displayValue === item.displayValue ? 'white' : 'black'
                                      }}>{item.value}</p>
                                    }
                                
                              </div>
                            ))}
                          </span>
                        </div>
                      ))}
                    </div>
                    <div className="rightPart">
                      <div className="imgWithBtns d-flex me-3">
                        <div className="incDecBtns d-flex flex-column justify-content-between">
                          <button className="incrementBtn border border-dark  px-1 bg-white text-dark" data-testid='cart-item-amount-increase' onClick={() => this.incrementProduct(product.id)}>+</button>
                          <p className={Style.itemSize} data-testid='cart-item-amount'>{product.quantity}</p>
                          <button className="decrementBtn border border-dark px-1 bg-white text-dark" data-testid='cart-item-amount-decrease' onClick={() => this.decrementProduct(product.id)}>-</button>
                        </div>
                        <img className={Style.cartImgs} src={product.gallery[0]} alt="Product Img" />
                      </div>
                    </div>
                  </div>
                ))}
              </div>
              <div className="placeOrder d-flex flex-column">
              <div className="total d-flex justify-content-between py-2" data-testid='cart-total'>
                <p className={`${Style.itemSize} fw-bold`} >Total : </p>
                <p className={`${Style.itemSize} fw-bold`} >{productsInCart.reduce((total, product) => total + product.prices[0].amount * product.quantity, 0).toFixed(2)}</p>
                </div>
              <div className="d-flex flex-column">
                {productsInCart.length===0 ?    
                         <button data-testid='cart-btn' className={`border border-transparent p-1 ${Style.totalBtnDis}`} disabled >Place Order</button>
                                 :
                  <button data-testid='cart-btn' className={`border border-transparent p-1 ${Style.totalBtn}`} onClick={this.placeOrder}>Place Order</button>
                }
                </div>
              </div>
            </div>
          </>
        )}
      </>
    );
  }
}
