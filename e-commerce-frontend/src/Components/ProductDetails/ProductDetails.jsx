import React, { Component } from "react";
import { gql } from "@apollo/client";
import client from "../Graphql/client";
import ReactHtmlParser from "react-html-parser";
import Styles from "./ProductDetails.module.css";
import { CategoryContext } from "../../Context/CategoryContext";
import { CartContext } from "../../Context/CartContext";

export default class ProductDetails extends Component {
  static contextType = CategoryContext;

  constructor(props) {
    super(props);
    this.state = {
      product: null,
      error: null,
      activeImageIndex: 0,
      selectedAttributes: {},
    };
  }

  componentDidMount() {
    const { pathname } = window.location;
    const productIdFromURL = pathname.replace("/product-details/", "");
    this.setState({ selectedProduct: productIdFromURL }, this.fetchProduct);
  }

  async fetchProduct() {
    try {
      const { data } = await client.query({
        query: gql`
          query ProductById($id: String) {
            Products(id: $id) {
              id
              name
              inStock
              description
              category
              brand
              gallery
              attributes {
                id
                name
                type
                items {
                  displayValue
                  value
                  id
                }
              }
              prices {
                amount
                currency {
                  label
                  symbol
                }
              }
            }
          }
        `,
        variables: { id: this.state.selectedProduct },
      });
      const product = data.Products[0];

      const selectedAttributes = {};
      product.attributes.forEach((attribute) => {
        selectedAttributes[attribute.id] = null; // Initialize with null
      });

      this.setState({ product, selectedAttributes });
    } catch (error) {
      this.setState({ error: error.message });
    }
  }

  chooseItem = (attributeId, item) => {
    this.setState((prevState) => ({
      selectedAttributes: {
        ...prevState.selectedAttributes,
        [attributeId]: item,
      },
    }));
  };

  render() {
    const { product, activeImageIndex, selectedAttributes } = this.state;

    const isAddToCartDisabled = product?.attributes?.length > 0 &&
      product.attributes.some(attribute => !selectedAttributes[attribute.id]);

    return (
      <CartContext.Consumer>
        {({ addToCart }) => (
          <>
            {product && (
              <div className="container mt-5 d-flex flex-wrap">
                <div className="d-flex flex-row flex-wrap w-100">
                  <div className={`${Styles.allImages} d-flex`}>
                    {product.gallery && (
                      <>
                        <div className="d-flex flex-column w-25 me-5">
                          {product.gallery.map((gallery, index) => (
                            <button
                              key={index}
                              className={Styles.imgsBtn}
                              onClick={() => this.setState({ activeImageIndex: index })}
                            >
                              <img src={gallery} className={`${Styles.imgsColumn} mb-1`} alt="Product" />
                            </button>
                          ))}
                        </div>
                        <div className={`${Styles.imgContainer}`}>
                          <div
                            id="carouselExampleControls"
                            className={`carousel slide ${Styles.carouselItem}`}
                            data-bs-ride="carousel"
                          >
                            <div className={`carousel-inner ${Styles.imgContainer}`}>
                              {product.gallery.map((gallery, index) => (
                                <div
                                  id="activeImg"
                                  className={`carousel-item ${index === activeImageIndex ? "active" : ""}`}
                                  key={index}
                                >
                                  <img src={gallery} className={`${Styles.img} d-block`} alt="Product" />
                                </div>
                              ))}
                            </div>
                            <button
                              className="carousel-control-prev"
                              type="button"
                              data-bs-target="#carouselExampleControls"
                              data-bs-slide="prev"
                            >
                              <span className="carousel-control-prev-icon bg-dark p-3" aria-hidden="true"></span>
                            </button>
                            <button
                              className="carousel-control-next"
                              type="button"
                              data-bs-target="#carouselExampleControls"
                              data-bs-slide="next"
                            >
                              <span className="carousel-control-next-icon bg-dark p-3" aria-hidden="true"></span>
                            </button>
                          </div>
                        </div>
                      </>
                    )}
                  </div>
                  <div className="txt ms-5 mt-5">
                    <h1 className="fs-3">{product.name}</h1>
                    <div className="attributes">
                      {product.attributes?.map((attribute) => (
                        <div key={attribute.id} className="my-4">
                          <h2 className="fs-4">{attribute.name}:</h2>
                          <div className="itemValues d-flex">
                            {attribute.items.map((item) => (
                              <button
                                key={item.id}
                                onClick={() => this.chooseItem(attribute.id, item)}
                                className="bg-transparent border-0"
                              >
                                {attribute.id === "Color" ? (
                                  <div
                                    className={`border border-dark p-1 m-1 border-1 ${Styles.item} 
                                    ${
                                      selectedAttributes[attribute.id] &&
                                      selectedAttributes[attribute.id].id === item.id
                                        ? Styles.colorSelectedItem
                                        : ""
                                    }`}
                                    style={{ backgroundColor: item.value }}
                                  ></div>
                                ) : (
                                  <p
                                    className={`border border-dark p-1 m-1 ${
                                      selectedAttributes[attribute.id] &&
                                      selectedAttributes[attribute.id].id === item.id
                                        ? Styles.selectedItem
                                        : ""
                                    }`}
                                  >
                                    {item.displayValue}
                                  </p>
                                )}
                              </button>
                            ))}
                          </div>
                        </div>
                      ))}
                    </div>
                    <div className="prices my-4">
                      <h2 className="fs-4">Prices:</h2>
                      {product.prices?.map((price, index) => (
                        <h3 className="fs-5" key={index}>
                          {price.currency.symbol}
                          {price.amount.toFixed(2)}
                        </h3>
                      ))}
                    </div>
                    <div className="addToCartBtn">
                      {product.inStock ? (
                        <button
                          className={`${Styles.cartBtn} ${isAddToCartDisabled ? Styles.cartBtnDisabled : ""} mb-5`}
                          onClick={() => addToCart(product, selectedAttributes)}
                          disabled={isAddToCartDisabled}
                        >
                          Add To Cart
                        </button>
                      ) : (
                        <button className={`${Styles.cartBtnDisabled} mb-5`} disabled>
                          Add To Cart
                        </button>
                      )}
                    </div>
                    <div className={`${Styles.description} dec`}>{ReactHtmlParser(product.description)}</div>
                  </div>
                </div>
              </div>
            )}
          </>
        )}
      </CartContext.Consumer>
    );
  }
}
