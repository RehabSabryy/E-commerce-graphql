import { createContext, useState } from "react";
export const CategoryContext = createContext()
export const CategoryProvider = ({ children }) => {
    const [selectedCategory, setSelectedCategory] = useState('all');
  
    return (
      <CategoryContext.Provider value={{ selectedCategory, setSelectedCategory }}>
        {children}
      </CategoryContext.Provider>
    );
  };