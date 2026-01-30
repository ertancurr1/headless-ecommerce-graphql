import { ApolloClient, InMemoryCache, HttpLink } from "@apollo/client";

// GraphQL API endpoint
const httpLink = new HttpLink({
  uri: "http://localhost/headless-ecommerce-graphql/backend/graphql.php",
});

// Create Apollo Client instance
export const apolloClient = new ApolloClient({
  link: httpLink,
  cache: new InMemoryCache(),
  defaultOptions: {
    watchQuery: {
      // Fetch from cache first, then network
      fetchPolicy: "cache-and-network",
    },
    query: {
      // Show errors in response instead of throwing
      errorPolicy: "all",
    },
  },
});
