export type EvalnorErrorCode = 'FORBIDDEN' | 'VALIDATION_ERROR' | 'NOT_FOUND' | 'CONFLICT';
export class EvalnorApplicationError extends Error {
  constructor(public readonly code: EvalnorErrorCode, message: string, public readonly details?: Record<string, unknown>) { super(message); this.name='EvalnorApplicationError'; }
}
export const forbidden = () => new EvalnorApplicationError('FORBIDDEN','Operation not permitted');
export const validationError = (message:string, details?:Record<string,unknown>) => new EvalnorApplicationError('VALIDATION_ERROR',message,details);
