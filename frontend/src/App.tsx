import { useQuery } from "@apollo/client";
import { GET_PRODUCTS } from "./graphql/queries";
import type { Product } from "./types";

const App = () => {
  const { data, loading, error } = useQuery<{ products: Product[] }>(
    GET_PRODUCTS,
    {
      variables: { limit: 5 },
    },
  );

  return (
    <div className="app">
      <header className="app__header">
        <h1>🛒 Headless E-Commerce</h1>
        <p>React + TypeScript + Vite + Apollo Client + GraphQL</p>
      </header>
      <main className="app__main">
        {loading && <p>Loading products...</p>}

        {error && (
          <div style={{ color: "red", textAlign: "center" }}>
            <p>Error connecting to API:</p>
            <code>{error.message}</code>
          </div>
        )}

        {data && (
          <div className="container">
            <h2 style={{ textAlign: "center", marginBottom: "1rem" }}>
              ✅ Connected! Found {data.products.length} products
            </h2>
            <div
              style={{
                display: "grid",
                gridTemplateColumns: "repeat(auto-fill, minmax(250px, 1fr))",
                gap: "1rem",
              }}
            >
              {data.products.map((product: Product) => (
                <div
                  key={product.id}
                  style={{
                    background: "white",
                    padding: "1rem",
                    borderRadius: "8px",
                    boxShadow: "0 2px 4px rgba(0,0,0,0.1)",
                  }}
                >
                  <h3 style={{ fontSize: "1rem", marginBottom: "0.5rem" }}>
                    {product.name}
                  </h3>
                  <p style={{ color: "#718096", fontSize: "0.875rem" }}>
                    SKU: {product.sku}
                  </p>
                  <p
                    style={{
                      color: "#3182ce",
                      fontWeight: "bold",
                      fontSize: "1.25rem",
                      marginTop: "0.5rem",
                    }}
                  >
                    ${product.effectivePrice.toFixed(2)}
                    {product.hasDiscount && (
                      <span
                        style={{
                          color: "#e53e3e",
                          fontSize: "0.875rem",
                          marginLeft: "0.5rem",
                          textDecoration: "line-through",
                        }}
                      >
                        ${product.price.toFixed(2)}
                      </span>
                    )}
                  </p>
                  <p
                    style={{
                      color: product.inStock ? "#38a169" : "#e53e3e",
                      fontSize: "0.875rem",
                      marginTop: "0.25rem",
                    }}
                  >
                    {product.inStock ? "✓ In Stock" : "✗ Out of Stock"}
                  </p>
                </div>
              ))}
            </div>
          </div>
        )}
      </main>
    </div>
  );
};

export default App;
