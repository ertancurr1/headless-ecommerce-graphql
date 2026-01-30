import { gql } from "@apollo/client";

// Get all attribute sets with attributes
export const GET_ATTRIBUTE_SETS = gql`
  query GetAttributeSets {
    attributeSets {
      id
      name
      attributes {
        id
        name
        code
        type
        options
      }
    }
  }
`;

// Get single attribute set
export const GET_ATTRIBUTE_SET = gql`
  query GetAttributeSet($id: ID!) {
    attributeSet(id: $id) {
      id
      name
      attributes {
        id
        name
        code
        type
        options
      }
    }
  }
`;
