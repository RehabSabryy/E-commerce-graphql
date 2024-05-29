import React, { Component } from 'react';
import './App.css';
import { createBrowserRouter , RouterProvider} from 'react-router-dom';
import Layout from './Components/Layout/Layout';
import Home from './Components/Home/Home';
import ProductDetails from './Components/ProductDetails/ProductDetails';
import NotFound from './Components/NotFound/NotFound';

export default class App extends Component {
  router = createBrowserRouter([
    {path: '/', element: <Layout />, children: [
        { index: true, element: <Home category="all" />  },
        { path: '/:category', element: <Home /> },
        { path: 'product-details/:id', element: <ProductDetails /> },
        {path:'*', element:<NotFound/>}
      ]
    }
  ]);

  render() {
    return (
      <>
        <RouterProvider router={this.router}>
        </RouterProvider>
      </>
    );
  }
}