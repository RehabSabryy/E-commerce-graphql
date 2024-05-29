import React, { Component, createContext } from 'react';

const CartContext = createContext();

class CartProvider extends Component {
  constructor(props) {
    super(props);
    this.state = {
      productsInCart: [],
      cartCount: 0,
    };
  }

  componentDidMount() {
    const storedCart = localStorage.getItem('cart');
    if (storedCart) {
      const productsInCart = JSON.parse(storedCart);
      this.setState({
        productsInCart,
        cartCount: this.calculateCartCount(productsInCart)
      });
    }
  }

  componentDidUpdate(prevProps, prevState) {
    if (prevState.productsInCart !== this.state.productsInCart) {
      localStorage.setItem('cart', JSON.stringify(this.state.productsInCart));
      this.setState({ cartCount: this.calculateCartCount(this.state.productsInCart) });
    }
  }

  calculateCartCount = (products) => {
    return products.reduce((count, product) => count + product.quantity, 0);
  };

  updateCart = (newCart) => {
    this.setState({ productsInCart: newCart });
  };

  addToCart = (product, selectedAttributes = {}) => {
    const { productsInCart } = this.state;
    const existingProductIndex = productsInCart.findIndex(
      (item) => item.id === product.id &&
        JSON.stringify(item.selectedAttributes) === JSON.stringify(selectedAttributes)
    );

    let updatedProductsInCart = [...productsInCart];

    if (existingProductIndex !== -1) {
      updatedProductsInCart[existingProductIndex].quantity++;
    } else {
      const productToAdd = {
        ...product,
        quantity: 1,
        selectedAttributes,
        attributes: product.attributes.map((attr) => ({
          ...attr,
          selectedItem: selectedAttributes[attr.id] || attr.items[0], // Ensure selectedItem is set
        })),
      };
      updatedProductsInCart.push(productToAdd);
    }

    this.updateCart(updatedProductsInCart);
  };

  render() {
    const { children } = this.props;
    const { productsInCart, cartCount } = this.state;

    return (
      <CartContext.Provider value={{ productsInCart, updateCart: this.updateCart, addToCart: this.addToCart, cartCount }}>
        {children}
      </CartContext.Provider>
    );
  }
}

export { CartContext, CartProvider };
