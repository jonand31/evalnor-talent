export type EvalnorLocale = 'fr' | 'en' | 'es';

export interface EvalnorCoreContext {
  tenantId: string;
  userId: string;
  locale: EvalnorLocale;
  permissions: string[];
}

export interface AuditEvent {
  action: string;
  entityType: string;
  entityId: string;
  occurredAt: string;
  metadata?: Record<string, unknown>;
}

export interface EvalnorAppEntitlement {
  appId: string;
  licensed: boolean;
  enabled: boolean;
  roles: string[];
  scopes: string[];
}

export interface EvalnorSyncOperation {
  operationId: string;
  entityType: string;
  entityId: string;
  action: string;
  payload: Record<string, unknown>;
  baseVersion?: string;
  deviceId?: string;
  createdAt?: string;
}

export interface EvalnorCorePort {
  authorize(context: EvalnorCoreContext, permission: string): Promise<boolean>;
  audit(context: EvalnorCoreContext, event: AuditEvent): Promise<void>;

  /** A Core license is mandatory before any app entitlement is usable. */
  hasCoreLicense(context: EvalnorCoreContext): Promise<boolean>;

  /** App licensing is independent from per-user access. */
  getAppEntitlement(
    context: EvalnorCoreContext,
    appId: string,
  ): Promise<EvalnorAppEntitlement>;

  /** Optional capability for desktop clients that permit temporary offline work. */
  pushSyncOperation?(
    context: EvalnorCoreContext,
    operation: EvalnorSyncOperation,
  ): Promise<Record<string, unknown>>;
}

export const EVALNOR_APP_ID = 'talent' as const;
