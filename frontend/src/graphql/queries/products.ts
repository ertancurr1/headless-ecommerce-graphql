import { gql } from "@apollo/client";

// Fragment for reusable product fields
export const PRODUCT_FRAGMENT = gql`
  fragment ProductFields on Product {
    id
    sku
    name
    description
    price
    specialPrice
    effectivePrice
    hasDiscount
    discountPercentage
    productType
    stockQuantity
    stockStatus
    inStock
  }
`;

// Get all products with optional filtering
export const GET_PRODUCTS = gql`
  ${PRODUCT_FRAGMENT}
  query GetProducts($filter: ProductFilterInput, $limit: Int, $offset: Int) {
    products(filter: $filter, limit: $limit, offset: $offset) {
      ...ProductFields
      images {
        id
        url
        altText
        isPrimary
      }
    }
  }
`;

// Get single product with full details
export const GET_PRODUCT = gql`
  ${PRODUCT_FRAGMENT}
  query GetProduct($id: ID!) {
    product(id: $id) {
      ...ProductFields
      attributes {
        id
        attributeId
        attributeName
        attributeCode
        value
      }
      images {
        id
        url
        altText
        position
        isPrimary
      }
      categories {
        id
        name
        slug
      }
      variants {
        id
        sku
        name
        price
        effectivePrice
        inStock
        attributes {
          attributeName
          value
        }
      }
    }
  }
`;

// Get product count
export const GET_PRODUCTS_COUNT = gql`
  query GetProductsCount($filter: ProductFilterInput) {
    productsCount(filter: $filter)
  }
`;
