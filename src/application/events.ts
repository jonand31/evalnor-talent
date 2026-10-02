export interface DomainEvent<T = unknown> { name: string; aggregateId: string; occurredAt: string; payload: T; }
export interface EventPublisher { publish<T>(event: DomainEvent<T>): Promise<void>; }
export class NullEventPublisher implements EventPublisher { async publish<T>(_event: DomainEvent<T>): Promise<void> {} }
