import React, { Component } from "react";
import { gql } from "@apollo/client";
import client from "../Graphql/client";
import Style from "./Home.module.css";
import { Link } from "react-router-dom";
import { CategoryContext } from "../../Context/CategoryContext";
import { CartContext } from "../../Context/CartContext";

export default class Home extends Component {
  static contextType = CategoryContext;

  constructor(props) {
    super(props);
    this.state = {
      products: [],
      previousCategory: null,
      error: null,
    };
  }

  componentDidMount() {
    // Fetch products initially
    this.fetchProducts();
  }

  componentDidUpdate(prevProps, prevState) {
    const { selectedCategory } = this.context;
    if (prevState.previousCategory !== selectedCategory) {
      this.fetchProducts();
      this.setState({ previousCategory: selectedCategory }); // Update previousCategory in state
    }
  }

  async fetchProducts() {
    const { selectedCategory } = this.context;
    try {
      let query;

      // Check if selectedCategory is 'all'
      if (selectedCategory === "all") {
        // If selectedCategory is 'all', fetch all products without filtering by category
        query = gql`
          query AllProducts {
            Products {
              id
              name
              category
              inStock
              gallery
              attributes {
                id
                name
                type
                items {
                  id
                  displayValue
                  value
                }
              }
              prices {
                amount
                currency {
                  symbol
                  label
                }
              }
            }
          }
        `;
      } else {
        // If selectedCategory is not 'all', fetch products based on the selected category
        query = gql`
          query ProductsByCategory($category: String!) {
            Products(category: $category) {
              id
              name
              category
              inStock
              gallery
              attributes {
                id
                name
                type
                items {
                  id
                  displayValue
                  value
                }
              }
              prices {
                amount
                currency {
                  symbol
                  label
                }
              }
            }
          }
        `;
      }

      const response = await client.query({
        query,
        variables: { category: selectedCategory },
      });

      this.setState({ products: response.data.Products });
    } catch (error) {
      this.setState({ error: error.message });
    }
  }
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
    const { selectedCategory } = this.context;
    const { products } = this.state;

    return (
      <>
        <div className="container">
          <h1 className={`${Style.heading} mt-5 ms-5`}>{selectedCategory}</h1>
          <div className="row">
            {products.map((product) => (
              <div className="col-12 col-md-6 col-lg-4 mb-4" key={product.id}>
                {product.inStock ? (
                  <div className={`${Style.box} d-flex flex-wrap box p-3 mx-3 my-5`}>
                    <Link to={`/product-details/${product.id}`} className="text-decoration-none">
                      <img src={product.gallery[0]} className={`${Style.productImg} d-flex`} alt="Product Img" />
                    </Link>
                    <div className="name&price pt-3 w-100">
                      <div className="d-flex justify-content-between">
                        <p className={Style.name}>{product.name}</p>
                        <CartContext.Consumer>
                          {({ addToCart }) => (
                            <button
                              className={`bg-transparent border-0 ${Style.iconDispay} ${Style.cartButton}`}
                              onClick={() => addToCart(product)}
                            >
                              <div className={`${Style.icon} rounded-circle px-2 pt-1 pb-2 align-self-center`}>
                                <svg
                                  xmlns="http://www.w3.org/2000/svg"
                                  width="16"
                                  height="16"
                                  fill="white"
                                  className="bi bi-cart"
                                  viewBox="0 0 16 16"
                                >
                                  <path d="M0 1.5A.5.5 0 0 1 .5 1H2a.5.5 0 0 1 .485.379L2.89 3H14.5a.5.5 0 0 1 .491.592l-1.5 8A.5.5 0 0 1 13 12H4a.5.5 0 0 1-.491-.408L2.01 3.607 1.61 2H.5a.5.5 0 0 1-.5-.5M3.102 4l1.313 7h8.17l1.313-7zM5 12a2 2 0 1 0 0 4 2 2 0 0 0 0-4m7 0a2 2 0 1 0 0 4 2 2 0 0 0 0-4m-7 1a1 1 0 1 1 0 2 1 1 0 0 1 0-2m7 0a1 1 0 1 1 0 2 1 1 0 0 1 0-2" />
                                </svg>
                              </div>
                            </button>
                          )}
                        </CartContext.Consumer>
                      </div>
                      <p className={Style.price}>
                        {product.prices[0].currency.symbol}
                        {product.prices[0].amount}
                      </p>
                    </div>
                  </div>
                ) : (
                  <Link to={`/product-details/${product.id}`}>
                    <div className={`${Style.box} box p-3 mx-3 my-5 position-absolute`}>
                      <div className={`${Style.outStock} d-flex justify-content-center align-items-center w-100`}>
                        <p className={`${Style.outStockText} position-absolute`}>Out of Stock</p>
                        <img src={product.gallery[0]} className={Style.productImg} alt="Product Img" />
                      </div>
                      <div className="name&price pt-3">
                        <p className={Style.name}>{product.name}</p>
                        <p className={Style.price}>
                          {product.prices[0].currency.symbol}
                          {product.prices[0].amount}
                        </p>
                      </div>
                    </div>
                  </Link>
                )}
              </div>
            ))}
          </div>
        </div>
      </>
    );
  }
}
