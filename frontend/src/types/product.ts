export interface ProductImage {
  id: string;
  url: string;
  altText: string | null;
  position: number;
  isPrimary: boolean;
}

export interface AttributeValue {
  id: string;
  attributeId: string;
  attributeName: string | null;
  attributeCode: string | null;
  value: string;
}

export interface Category {
  id: string;
  name: string;
  slug: string;
  description: string | null;
  parentId: string | null;
  isRoot: boolean;
  isActive: boolean;
  path?: string;
  children?: Category[];
}

export interface Product {
  id: string;
  sku: string;
  name: string;
  description: string | null;
  price: number;
  specialPrice: number | null;
  effectivePrice: number;
  hasDiscount: boolean;
  discountPercentage: number | null;
  productType: "simple" | "configurable";
  stockQuantity: number;
  stockStatus: "in_stock" | "out_of_stock";
  inStock: boolean;
  images?: ProductImage[];
  attributes?: AttributeValue[];
  categories?: Category[];
  variants?: Product[];
}

export interface ProductFilterInput {
  categoryId?: string;
  minPrice?: number;
  maxPrice?: number;
  productType?: string;
  inStock?: boolean;
}
