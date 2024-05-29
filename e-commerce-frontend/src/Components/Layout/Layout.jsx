import React, { Component } from 'react';
import Navbar from '../Navbar/Navbar';
import { Outlet } from 'react-router-dom';
import { CategoryContext } from '../../Context/CategoryContext';
import { CartContext } from '../../Context/CartContext';

export default class Layout extends Component {
  static contextType = CartContext;

  constructor(props) {
    super(props);
    this.state = {
      selectedCategory: 'all',
    };
  }

  setSelectedCategory = (category) => {
    this.setState({ selectedCategory: category });
  };

  render() {
    const { selectedCategory } = this.state;
    const { cartCount, productsInCart, updateCart } = this.context;

    return (
      <CategoryContext.Provider value={{
        selectedCategory,
        setSelectedCategory: this.setSelectedCategory,
      }}>
        <div className="container-fluid px-0">
          <Navbar
            cartCount={cartCount}
            productsInCart={productsInCart}
            updateCart={updateCart}
          />
          <Outlet />
        </div>
      </CategoryContext.Provider>
    );
  }
}
