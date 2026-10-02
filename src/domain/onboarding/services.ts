import type { OnboardingPlan } from './model.js';
export const onboardingProgress=(completedTaskIds:readonly string[],plan:OnboardingPlan)=>plan.taskIds.length===0?1:plan.taskIds.filter(id=>completedTaskIds.includes(id)).length/plan.taskIds.length;
