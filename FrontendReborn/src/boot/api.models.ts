export type MongoId = { $oid: string };

export interface WAFRule {
  name?: string;
  expression:
    | {
        type: 'regex';
        field: string;
        regex: string;
      }
    | {
        type: 'function';
        code: string;
      };
  response?:
    | {
        type: 'text';
        content: string;
      }
    | {
        type: 'replace';
        regex: string;
        replacement: string;
      };
}

export interface WAFListResult {
  status: true;
  data: (WAFRule & { _id: MongoId })[];
}
