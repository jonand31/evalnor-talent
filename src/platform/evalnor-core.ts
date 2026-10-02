export interface EvalnorCoreContext {
  tenantId: string;
  userId: string;
  locale: 'fr' | 'en' | 'es';
  permissions: string[];
}

export interface AuditEvent {
  action: string;
  entityType: string;
  entityId: string;
  occurredAt: string;
  metadata?: Record<string, unknown>;
}

export interface EvalnorCorePort {
  authorize(context: EvalnorCoreContext, permission: string): Promise<boolean>;
  audit(context: EvalnorCoreContext, event: AuditEvent): Promise<void>;
}
