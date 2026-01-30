export interface Attribute {
  id: string;
  name: string;
  code: string;
  type: "text" | "select";
  options: string[];
}

export interface AttributeSet {
  id: string;
  name: string;
  attributes: Attribute[];
}
