import React, { Component } from "react";
import { Link } from "react-router-dom";
import Style from "./Navbar.module.css";
import { CategoryContext } from "../../Context/CategoryContext";
import { gql } from "@apollo/client";
import client from "../Graphql/client";
import Cart from "../Cart/Cart";
import { CartContext } from "../../Context/CartContext";

const GetCategories = gql`
  {
    Categories {
      name
    }
  }
`;

export default class Navbar extends Component {
  constructor(props) {
    super(props);
    this.state = {
      categories: [],
      error: null,
      isCartOpen: false,
      
    };
  }

  componentDidMount() {
    this.fetchCategories();
  }

  async fetchCategories() {
    try {
      const response = await client.query({ query: GetCategories });
      this.setState({ categories: response.data.Categories });
    } catch (error) {
      this.setState({ error: error.message });
    }
  }

  handleCategorySelect = (category, setSelectedCategory) => {
    setSelectedCategory(category);
  };

  toggleCart = () => {
    this.setState((prevState) => ({
        isCartOpen: !prevState.isCartOpen,
    }) , () => {
      console.log(this.props.productsInCart);

    });
};

  render() {
    const { categories, isCartOpen} = this.state;

    return (
      <CategoryContext.Consumer>
        {({ selectedCategory, setSelectedCategory }) => (
          <CartContext.Consumer>
            {({  productsInCart, updateCart, cartCount }) => (
              <>
                <nav className={`navbar navbar-expand-lg navbar-light py-4 ${Style.navbar}`}>
                  <div className={`container-fluid d-flex px-5`}>
                    <div className="links">
                      {categories.map((category) => (
                        <Link
                        data-testid='category-link active-category-link'
                          key={category.name}
                          className={`${Style.navbarBrand} pe-3`}
                          to={`/${category.name}`}
                          onClick={() => this.handleCategorySelect(category.name, setSelectedCategory)}
                        >
                          {category.name}
                        </Link>
                      ))}
                    </div>
                    <div>
                      <img
                        src={`${process.env.PUBLIC_URL}/images/logo.png`}
                        className="logo"
                        alt="Logo"
                      />
                    </div>
                    <div className="position-relative pe-5">
                      <button className="bg-transparent border-0 text-dark position-relative" onClick={this.toggleCart}>
                        <svg
                          xmlns="http://www.w3.org/2000/svg"
                          width="20"
                          height="20"
                          fill="currentColor"
                          className="bi bi-cart"
                          viewBox="0 0 16 16"
                        >
                          <path d="M0 1.5A.5.5 0 0 1 .5 1H2a.5.5 0 0 1 .485.379L2.89 3H14.5a.5.5 0 0 1 .491.592l-1.5 8A.5.5 0 0 1 13 12H4a.5.5 0 0 1-.491-.408L2.01 3.607 1.61 2H.5a.5.5 0 0 1-.5-.5M3.102 4l1.313 7h8.17l1.313-7zM5 12a2 2 0 1 0 0 4 2 2 0 0 0 0-4m7 0a2 2 0 1 0 0 4 2 2 0 0 0 0-4m-7 1a1 1 0 1 1 0 2 1 1 0 0 1 0-2m7 0a1 1 0 1 1 0 2 1 1 0 0 1 0-2" />
                        </svg>
                        {cartCount >=0 && (
                          <span className={`${Style.itemCount} px-2 rounded-circle bg-dark text-white`}>
                            {cartCount}
                          </span>
                        )}
                      </button>
                    </div>
                  </div>
                </nav>
                <Cart
                  isCartOpen={isCartOpen}
                  cartProducts={productsInCart}
                  toggleCart={this.toggleCart}
                  updateCart={updateCart}
                  cartCount={cartCount}
                />
              </>
            )}
          </CartContext.Consumer>
        )}
      </CategoryContext.Consumer>
    );
  }
}
