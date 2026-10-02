export interface OnboardingPlan { id:string; workerId:string; templateId:string; startsAt:string; taskIds:string[]; status:'planned'|'active'|'completed'|'cancelled'; }
