import { gql } from "@apollo/client";

// Get category tree (hierarchical)
export const GET_CATEGORY_TREE = gql`
  query GetCategoryTree {
    categoryTree {
      id
      name
      slug
      description
      isRoot
      children {
        id
        name
        slug
        description
      }
    }
  }
`;

// Get all categories (flat list)
export const GET_CATEGORIES = gql`
  query GetCategories {
    categories {
      id
      name
      slug
      description
      parentId
      isRoot
      isActive
    }
  }
`;

// Get single category with children
export const GET_CATEGORY = gql`
  query GetCategory($id: ID!) {
    category(id: $id) {
      id
      name
      slug
      description
      parentId
      isRoot
      path
      children {
        id
        name
        slug
      }
    }
  }
`;

// Get category by slug
export const GET_CATEGORY_BY_SLUG = gql`
  query GetCategoryBySlug($slug: String!) {
    categoryBySlug(slug: $slug) {
      id
      name
      slug
      description
      path
      children {
        id
        name
        slug
      }
    }
  }
`;
